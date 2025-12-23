
"""
Cette API permet :  
- de **consulter des statistiques** sur les données clients:
    min max médian moyenne écart-type du montant distribué par chaque client
    la somme et la moyenne de tout les montant pour chaque devise source et le nombre de client par devise souce
    Top 10 clients les plus fréquents
    le nombre de transactions par moi

- de **prédire le segment** d’un client (A, B, C…) à partir d’un modèle **XGBoost** pré-entraîné selon les données RFM de clients et devises. 
    Réception des données client → JSON respectant ClientData.
    Conversion en DataFrame Pandas pour traitement.
    Vérification des colonnes nécessaires au modèle (feature_cols).
    Encodage des variables catégoriques (MostUsedSourceCurrency, MostUsedDestCurrency) en nombres.
    Mise à l’échelle des variables numériques avec le scaler.
    Conversion en DMatrix pour XGBoost.
    Prédiction avec le modèle XGBoost (bst) → code numérique.
    Décodage du code en segment réel (A, B, C, …) avec le_segment
 """

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import pandas as pd
import numpy as np
import joblib
import xgboost as xgb
import os

# Initialisation de l'application FastAPI
#Création de notre API
app = FastAPI()

# Configurer CORS il contrôle qui a le droit d’appeler ton API.
#CORS ontrôle qui a le droit d’appeler ton API
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],         # Qui peut accéder → "*" = tout le monde
    allow_credentials=True,      # Autorise cookies / tokens
    allow_methods=["*"],         # Toutes les méthodes HTTP (GET, POST, PUT...)
    allow_headers=["*"],         # Tous les headers (ex: Authorization)
)

# Chemins des fichiers de données et du modèle
DATA_PATH = "client_360_profile.csv"                   #CSV = Comma-Separated Values → valeurs séparées par des virgules C’est un format texte simple pour stocker des tableaux
MODEL_PATH = "xgb_client_model.pkl"

# Charger les données et le modèle
try:
    df = pd.read_csv(DATA_PATH)                        # lit le fichier CSV et le transforme en DataFrame Pandas ( comme feuille de calcul Excel)
    model_dict = joblib.load(MODEL_PATH)               # charge un fichier MODEL_PATH qui contient des objets Python sauvegardés dans un dictionnaire
    # Récupérer les objets du dictionnaire
    bst = model_dict['model']                          # bst → le modèle XGBoost prêt à faire des prédictions.
    scaler = model_dict['scaler']                      # scaler → objet pour mettre à l’échelle les données (normalisation).
    label_encoders = model_dict['label_encoders']      
    feature_cols = model_dict['feature_cols']
    le_segment = model_dict['le_segment']
except FileNotFoundError as e:
    raise Exception(f"Erreur de chargement des fichiers : {str(e)}")

# Modèle Pydantic pour valider les données d'entrée du client
class ClientData(BaseModel):            #BaseModel est une bibliothèque que FastAPI utilise pour valider et typer les données
    Recency: int
    Frequency: int
    Monetary: float
    UniqueSourceCurrencies: int
    UniqueDestCurrencies: int
    AvgTransactionAmount: float
    TransactionStdDev: float
    FirstTransactionDaysAgo: int
    MostUsedSourceCurrency: str
    MostUsedDestCurrency: str

# ------------------ ENDPOINTS STATISTIQUES ------------------

# Distribution des montants  Monetary = montant total dépensé ou généré par un client
@app.get("/api/stats/amount_distribution")
async def get_amount_distribution_stats():

    try:
        stats = {
            "mean": float(df['Monetary'].mean()),       # médiane 
            "median": float(df['Monetary'].median()),
            "min": float(df['Monetary'].min()),
            "max": float(df['Monetary'].max()),
            "std": float(df['Monetary'].std())         #écart-type
        }
        return stats
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erreur lors du calcul des stats : {str(e)}")

# Montant par devise source
@app.get("/api/stats/amount_by_currency_from")
async def get_amount_by_currency_from_stats():
    try:
        grouped = df.groupby('MostUsedSourceCurrency')['Monetary'].agg(['sum', 'mean', 'count']).reset_index()
        return grouped.to_dict(orient='records')
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erreur lors du calcul des stats : {str(e)}")

#1. regrouper les client par type de devise DT, euro... le plus utilisé
#2. compter la somme et la moyenne de tout les montant pour chaque devise et compter le nombre de client par devise
#3. reset_index(): Transforme le résultat en DataFrame normal, avec les devises en colonne au lieu d’être en index

# Top 10 clients les plus fréquents
@app.get("/api/stats/top_clients")
async def get_top_clients_stats():
    try:
        top_clients = df['ClientID'].value_counts().head(10)
        return top_clients.to_dict()
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erreur lors du calcul des stats : {str(e)}")

# df['ClientID'] On prend la colonne ClientID du DataFrame.
# .value_counts() Compte combien de fois chaque client apparaît dans le DataFrame.
# .head(10) Prend les 10 clients les plus fréquents (leurs id et le nombre de fois qu'ils apparaient)

# compter le nombre de transactions par moi
@app.get("/api/stats/transactions_over_time")
async def get_transactions_over_time_stats():
    try:
        df['Date'] = pd.to_datetime(df['Date'], errors='coerce')              #Convertit la colonne Date en format datetime utilisable par Pandas
        transactions_per_month = df.groupby(df['Date'].dt.to_period('M')).size() 
        # df['Date'].dt.to_period('M') → transforme chaque date en période mensuelle
        # groupby(...) → regroupe toutes les transactions par mois.
        # .size() → compte le nombre de transactions dans chaque mois.
        return transactions_per_month.to_dict()
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erreur lors du calcul des stats : {str(e)}")

# ------------------ ENDPOINT DE PREDICTION ------------------

#attend en entrée un objet JSON qui doit respecter la structure du modèle ClientData
@app.post("/api/predict_segment")
async def predict_segment(client: ClientData):
    try:
        # 1.on a convertie les entrées (données de client en dataFrame)                                                                                  
        new_df = pd.DataFrame([client.dict()])

        # 2. on les comparent avec la liste des colonnes nécessaires au modèle de prédiction
        missing_cols = [col for col in feature_cols if col not in new_df.columns]
        if missing_cols:
            raise HTTPException(status_code=400, detail=f"Colonnes manquantes : {missing_cols}")

        X_new = new_df[feature_cols].copy()

        # Encoder les variables catégoriques
        # Un Label_encoder sert à transformer des valeurs textuelles (catégories) en valeurs numériques pour que le modèle de machine learning puisse les comprendre.
        categorical_cols = ['MostUsedSourceCurrency', 'MostUsedDestCurrency']
        for col in categorical_cols:
            if col in label_encoders:
                le = label_encoders[col]
                X_new[col] = X_new[col].astype(str).map(
                    lambda x: x if x in le.classes_ else le.classes_[0] #Si le client envoie une valeur que le modèle ne connaît pas, on la remplace par la première valeur
                )
                X_new[col] = le.transform(X_new[col].astype(str))        #Transformer le texte en nombre "USD" → 2

        # Mettre à l'échelle les variables numériques
        numeric_cols = [c for c in X_new.columns if c not in categorical_cols]
        X_new[numeric_cols] = scaler.transform(X_new[numeric_cols])

        # Convertir en DMatrix pour XGBoost
        dnew = xgb.DMatrix(X_new)

        # Prédiction
        y_pred_encoded = bst.predict(dnew)
        y_pred_segment = le_segment.inverse_transform(y_pred_encoded.astype(int))
        # Le segment prédit par le modèle XGBoost. Exemple : "A" (client très actif), "B" (client moyen), "C" (client peu actif).

        return {"client_data": client.dict(), "predicted_segment": y_pred_segment[0]}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erreur lors de la prédiction : {str(e)}")

# ------------------ HEALTH CHECK ------------------

@app.get("/api/health")
async def health_check():
    return {"status": "API is running"}


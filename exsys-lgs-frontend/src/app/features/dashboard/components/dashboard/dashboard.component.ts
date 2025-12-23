import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../../core/services/auth.service';
import { TitleService } from '../../../../shared/title.service';

@Component({
  selector: 'app-dashboard',
  standalone: false,
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent implements OnInit {
  constructor(public authService :  AuthService , public titleService : TitleService) {}

  
  ngOnInit(): void {
    this.titleService.setTitle('Dashboard');
  }

}

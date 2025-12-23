import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-reset-password',
  standalone:false,
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.css']
})
export class ResetPasswordComponent {
  step: 'request' | 'reset' = 'request';
  requestForm: FormGroup;
  resetForm: FormGroup;
  isLoading = false;
  errorMessage = '';
  successMessage = '';
  token: string = '';
  showPassword = false;
  showConfirmPassword = false;
  requestCooldown = false;


  constructor(
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private userService: UserService
  ) {
    this.requestForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });

    this.resetForm = this.fb.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      confirmPassword: ['', [Validators.required]]
    }, { validator: this.passwordMatchValidator });

    // Check if token exists in URL
    this.route.queryParams.subscribe(params => {
      if (params['token']) {
        this.token = params['token'];
        this.step = 'reset';
      }
    });
  }

  passwordMatchValidator(g: FormGroup) {
    return g.get('password')?.value === g.get('confirmPassword')?.value
      ? null : { mismatch: true };
  }

  onRequestSubmit(): void {
    if (this.requestForm.invalid || this.requestCooldown) return;
  
    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = '';
    this.requestCooldown = true; // throttle future requests
  
    this.userService.sendResetPassword(this.requestForm.value.email).subscribe({
      next: () => {
        this.isLoading = false;
        this.successMessage = 'Reset password link has been sent to your email. Please check your inbox.';
      },
      error: (error) => {
        this.isLoading = false;
        this.errorMessage = error.error?.message || 'Failed to send reset link. Please try again.';
      }
    });
  
    // Allow resending after 5 seconds
    setTimeout(() => this.requestCooldown = false, 5000);
  }
  

  onResetSubmit(): void {
    if (this.resetForm.valid && this.token) {
      this.isLoading = true;
      this.errorMessage = '';

      this.userService.resetPassword({
        token: this.token,
        password: this.resetForm.value.password
      }).subscribe({
        next: () => {
          this.router.navigate(['/login'], {
            queryParams: { resetSuccess: true }
          });
        },
        error: (error) => {
          this.isLoading = false;
          this.errorMessage = error.error.message || 'Failed to reset password. Please try again.';
        }
      });
    }
  }

  togglePasswordVisibility(field: 'password' | 'confirmPassword'): void {
    if (field === 'password') {
      this.showPassword = !this.showPassword;
    } else {
      this.showConfirmPassword = !this.showConfirmPassword;
    }
  }
}
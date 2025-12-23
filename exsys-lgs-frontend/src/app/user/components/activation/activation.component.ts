import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-activation',
  templateUrl: './activation.component.html',
  styleUrls: ['./activation.component.css'],
  standalone:false
})
export class ActivationComponent implements OnInit {
  form: FormGroup;
  isLoading = false;
  token: string = '';
  errorMessage = '';
  invitationInvalid = false;

  invitationData: {
    valid: boolean;
    email?: string;
  } | null = null;

  showPassword = false;
  showConfirmPassword = false;

  constructor(
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private userService: UserService
  ) {
    this.form = this.fb.group({
      username: ['', [Validators.required, Validators.minLength(4)]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      confirmPassword: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit(): void {
    this.token = this.route.snapshot.queryParams['token'] || '';
    console.log("token is " +this.token)
    if (this.token) {
      this.validateTokenAndLoadData();
    } else {
      this.invitationInvalid = true;
    }
  }

  validateTokenAndLoadData(): void {
    this.userService.validateInvitationToken(this.token).subscribe({
      next: (data) => {
        this.invitationData = data;
        this.invitationInvalid = !data.valid;
        this.form.patchValue({
          username: data.email ?? ""
        });
      },
      error: () => {
        this.invitationInvalid = true;
      }
    });
  }

  passwordMatchValidator(g: FormGroup) {
    return g.get('password')?.value === g.get('confirmPassword')?.value
      ? null : { mismatch: true };
  }

  onSubmit(): void {
    if (this.form.valid && this.token) {
      this.isLoading = true;
      this.errorMessage = '';

      const payload = {
        username: this.form.value.username,
        password: this.form.value.password
      };

      this.userService.acceptInvitation(this.token, payload).subscribe({
        next: () => this.router.navigate(['/login']),
        error: (error) => {
          this.isLoading = false;
          this.errorMessage = error.error.message || 'Activation failed. Please try again.';
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

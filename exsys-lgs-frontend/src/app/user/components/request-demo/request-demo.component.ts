import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-request-demo',
  templateUrl: './request-demo.component.html',
  styleUrls: ['./request-demo.component.css'],
  standalone: false
})
export class RequestDemoComponent implements OnInit {
  form: FormGroup;
  isLoading = false;
  success = false;
  submitted = false;

  constructor(private fb: FormBuilder) {
    this.form = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      agency: ['', [Validators.required, Validators.minLength(2)]],
      message: ['']
    });
  }

  ngOnInit(): void {}

  onSubmit(): void {
    this.submitted = true;

    if (this.form.valid) {
      this.isLoading = true;

      setTimeout(() => {
        this.isLoading = false;
        this.success = true;
        this.form.reset();
        this.submitted = false;
      }, 2000); // Simulated API call
    }
  }

  get f() {
    return this.form.controls;
  }
}

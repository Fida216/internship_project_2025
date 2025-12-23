// features/layout/layout.component.ts
import { Component } from '@angular/core';
import { AuthService } from '../../core/services/auth.service';
import { TitleService } from '../../shared/title.service';

@Component({
  selector: 'app-features-layout',
  standalone: false,  
  template: `
    <div class="flex h-screen">
      <app-sidebar></app-sidebar>
      <div class="flex flex-col flex-1">
        <app-top-navbar></app-top-navbar>
        <main class="flex-1 bg-gray-100 p-6 overflow-y-auto">
          <router-outlet></router-outlet>
        </main>
      </div>
    </div>
  `,
  styleUrls: ['./layout.component.css']
})
export class LayoutComponent {
  constructor(public authService: AuthService, public titleService: TitleService) {}
}
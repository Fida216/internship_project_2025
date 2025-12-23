// sidebar.component.ts
import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../core/services/auth.service';
import {
  AdminLinks,
  AgentLinks,
  GeneralLinks,
  MarketingLinks,
  SidebarLink
} from './sidebar.links';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css'],
  standalone: false
})

export class SidebarComponent implements OnInit {
  isCollapsed = false;
    isAgent = false;
  isAdmin = false;
  currentYear = new Date().getFullYear(); 

  generalLinks: SidebarLink[] = GeneralLinks;
  marketingLinks: SidebarLink[] = MarketingLinks;
  adminLinks: SidebarLink[] = [];
  agentLinks: SidebarLink[] = [];

  constructor(public authService: AuthService) {}

  sections : any [] | null= null;

  ngOnInit(): void {
    const role = this.authService.getUserRole();
    this.isAdmin = role === 'admin';
    this.isAgent = role === 'agent';

    console.log('User role:', role);
    console.log('Is Admin:', this.isAdmin);
    console.log('Is Agent:', this.isAgent);

    
    if (this.isAdmin) this.adminLinks = AdminLinks;
    if (this.isAgent) this.agentLinks = AgentLinks;
    

    this.sections = [
      { title: 'General', condition: true, links: this.generalLinks },
      { title: 'Admin', condition: this.isAdmin, links: this.adminLinks },
      { title: 'Agent', condition: this.isAgent, links: this.agentLinks },
      { title: 'Marketing', condition: !this.isAdmin, links: this.marketingLinks }
    ];

    console.log("Admin Links", AdminLinks);

  }

  toggleSidebar() {
    this.isCollapsed = !this.isCollapsed;
  }
}

// shared/sidebar/sidebar.links.ts

export interface SidebarLink {
  label: string;
  icon: string;
  route: string;
  permission?: string[]; // Optional permission check
}

export const AdminLinks: SidebarLink[] = [
    { label: 'Clients', icon: 'fas fa-users', route: '/clients', permission: ['admin'] },
  { label: 'Users', icon: 'fas fa-users-cog', route: '/users', permission: ['admin'] },
  { label: 'Exchange Offices', icon: 'fas fa-building', route: '/offices', permission: ['admin'] },
  { label: 'Access Control', icon: 'fas fa-key', route: '/access', permission: ['admin'] },
  { label: 'Audit Logs', icon: 'fas fa-clipboard-list', route: '/audit', permission: ['admin'] },
  { label: 'Performance', icon: 'fas fa-chart-line', route: '/reports', permission: ['admin'] },
  { label: 'KPIs', icon: 'fas fa-calculator', route: '/kpi', permission: ['admin'] },
  { label: 'Revenue Impact', icon: 'fas fa-hand-holding-usd', route: '/revenue-impact', permission: ['admin'] },
];

export const AgentLinks: SidebarLink[] = [
  { label: 'My Clients', icon: 'fas fa-address-book', route: '/my-clients', permission: ['agent'] },
  { label: 'Alerts', icon: 'fas fa-bell', route: '/alerts', permission: ['agent'] },
  { label: 'Sales Targets', icon: 'fas fa-bullseye', route: '/targets', permission: ['agent'] },
  { label: 'Transactions', icon: 'fas fa-exchange-alt', route: '/transactions', permission: ['agent'] },
];

export const GeneralLinks: SidebarLink[] = [
  { label: 'Dashboard', icon: 'fas fa-tachometer-alt', route: '/dashboard' },
];

export const MarketingLinks: SidebarLink[] = [
  { label: 'Campaigns', icon: 'fas fa-bullhorn', route: '/marketing/campaigns' },
  { label: 'Quick Messages', icon: 'fas fa-envelope', route: '/marketing/messages' },
  { label: 'Segmentation', icon: 'fas fa-chart-pie', route: '/marketing/segmentation' },
];
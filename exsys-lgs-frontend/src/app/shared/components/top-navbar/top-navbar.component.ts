import { Component, ElementRef, HostListener, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { AuthService } from '../../../core/services/auth.service';
import { appRoutes } from '../../../app.routes';
import { TitleService } from '../../title.service';
import { Subscription } from 'rxjs';
import { UserService } from '../../../user/services/user.service';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-top-navbar',
  templateUrl: './top-navbar.component.html',
  styleUrls: ['./top-navbar.component.css'],
  standalone:false
})
export class TopNavbarComponent implements OnInit , OnDestroy {
  title: string = 'Customer 360';
  private titleSubscription!: Subscription;
  showDropdown: boolean = false;
  @ViewChild('userDropdownTrigger') userDropdownTrigger!: ElementRef;
  @ViewChild('dropdownMenu') dropdownMenu!: ElementRef;
 
  user: any = {
    name: '',
    role: '',
    avatar: '',
    agency: ''
  };


  constructor(
    private authService: AuthService,
    private titleService: TitleService,
    private userService: UserService,
    private sanitizer : DomSanitizer
  ) {}


  ngOnInit(): void {
    this.loadUserInfo();
    this.titleSubscription = this.titleService.currentTitle.subscribe(title => {
      this.title = title;
    });
  }

  loadUserInfo(): void {
    this.userService.getUserProfile().subscribe({
      next: (res) => {
        this.user.name = `${res.user.firstName} ${res.user.lastName}`;
        this.user.role = res.user.role;
        this.user.agency = res.user.exchangeOffice?.name || 'N/A';
        this.loadProfilePicture();
      },
      error: (err) => {
        console.error('Error loading user profile:', err);
      }
    });
  }


  toggleDropdown(event: MouseEvent) {
    event.stopPropagation();
    this.showDropdown = !this.showDropdown;
  }


  loadProfilePicture(): void {
    this.userService.getProfilePicture().subscribe({
      next: (blob: Blob) => {
        const objectURL = URL.createObjectURL(blob);
        this.user.avatar = this.sanitizer.bypassSecurityTrustUrl(objectURL);
      },
      error: () => {
        console.error('Error loading profile picture, using default avatar');
        this.user.avatar = 'assets/avatar-default.svg';
      }
    });
  }


  @HostListener('document:click', ['$event'])
  onClickOutside(event: MouseEvent) {
    if (!this.userDropdownTrigger.nativeElement.contains(event.target) && 
        (!this.dropdownMenu || !this.dropdownMenu.nativeElement.contains(event.target))) {
      this.showDropdown = false;
    }
  }



  ngOnDestroy() {
    this.titleSubscription.unsubscribe();
  }

  logout() {
    // Your logout logic
    this.showDropdown = false;
    this.authService.logout();

  }
}



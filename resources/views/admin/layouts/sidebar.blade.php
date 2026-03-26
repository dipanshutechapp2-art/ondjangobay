<aside class="main-sidebar sidebar-light-primary elevation-4 admin-dashboard-sidebar">

<!-- Brand Logo -->

<a href="{{ url('/admin/dashboard') }}" class="brand-link">

  <img src="{{ asset('admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">

  <span class="brand-text font-weight-light">Administrator</span>

</a>


<!-- Sidebar -->

<div class="sidebar">

  <!-- Sidebar user panel (optional) -->

  <div class="user-panel mt-3 pb-3 mb-3 d-flex">

    <div class="image">

      <img src="{{ asset('admin/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">

    </div>

    <div class="info">

      <a href="{{ url('/admin/dashboard') }}" class="d-block">@if(auth()->check()) {{ Auth::user()->name }} @else Testing @endif</a>

    </div>

  </div>

  <!-- Sidebar Menu -->

  <nav class="mt-2">

    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

      <!-- Add icons to the links using the .nav-icon class

           with font-awesome or any other icon font library -->

      <li class="nav-item menu-open">

        <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">

          <i class="nav-icon fas fa-tachometer-alt"></i>

          <p>

            Dashboard

          </p>

        </a>

      </li>

      <li class="nav-item ">
                    <a href="{{ url('/admin/users') }}"
                        class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Users

                        </p>
                    </a>
                </li>
             <li class="nav-item ">
                    <a href="{{ url('/admin/wallet') }}"
                        class="nav-link {{ Request::is('admin/wallet*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>
                           Users Wallet
                        </p>
                    </a>
                </li>
                     <li class="nav-item ">
                    <a href="{{ url('/admin/vendor') }}"
                        class="nav-link {{ Request::is('admin/vendor') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Vendor

                        </p>
                    </a>
                </li>
               <li class="nav-item ">
                    <a href="{{ url('/admin/categories') }}"
                        class="nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Category

                        </p>
                    </a>
                </li>
				
				<li class="nav-item ">
                    <a href="{{ url('/admin/newsletters') }}"
                        class="nav-link {{ Request::is('admin/newsletters*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Newsletters
                        </p>
                    </a>
                </li>
			<li class="nav-item ">
                    <a href="{{ url('/admin/coupon') }}"
                        class="nav-link {{ Request::is('admin/coupon*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Coupon

                        </p>
                    </a>
                </li>
             <li class="nav-item ">
                    <a href="{{ url('/admin/store') }}"
                        class="nav-link {{ Request::is('admin/store*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Store

                        </p>
                    </a>
                </li>
				{{-- <li class="nav-item ">
                    <a href="{{ url('/admin/wholesale2b/products/import') }}"
                        class="nav-link {{ Request::is('wholesale2b/products/import*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Import Products

                        </p>
                    </a>
                </li> --}}
				<li class="nav-item ">
                    <a href="{{ url('/admin/products') }}"
                        class="nav-link {{ Request::is('admin/products*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Products

                        </p>
                    </a>
                </li> 
				
				
				<li class="nav-item ">
                    <a href="{{ url('/admin/attributes') }}"
                        class="nav-link {{ Request::is('admin/attributes*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            Attributes
                        </p>
                    </a>
               </li>
			   <li class="nav-item ">
                    <a href="{{ url('/admin/upload-images') }}"
                        class="nav-link {{ Request::is('admin/upload-images*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Upload Product Images
                        </p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ url('/admin/brands') }}"
                        class="nav-link {{ Request::is('admin/brands*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            Brand
                        </p>
                    </a>
               </li>
                <li class="nav-item ">
                    <a href="{{ url('/admin/orders') }}"
                        class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Orders

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/admin/partner-orders') }}"
                        class="nav-link {{ Request::is('admin/partner-orders*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Campaign Orders

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/admin/shipping-options') }}"
                        class="nav-link {{ Request::is('admin/shipping-options*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Shipping Options
                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/admin/shipping-prices') }}"
                        class="nav-link {{ Request::is('admin/shipping-prices*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Shipping Prices

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/admin/activity-log-history') }}"
                        class="nav-link {{ Request::is('admin/activity-log-history*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                           Activity Log Hisoty
                        </p>
                    </a>
                </li>

                    <li
                    class="nav-item {{ Request::is('admin/blog-category*') || Request::is('admin/blog*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ Request::is('admin/blog-category*') || Request::is('admin/blog*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-blog"></i>
                        <p>
                            Manage Blogs
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" @if (Request::is('admin/blog-category*') || Request::is('admin/blog*')) style="display: block;" @endif>
                        <li class="nav-item ">
                            <a href="{{ url('admin/blog-category') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a href="{{ url('admin/blog') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Blogs</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Theme Setting -->

				{{-- <li class="nav-item ">
            <a href="{{ url('admin/mail-setting') }}"
                class="nav-link {{ Request::is('admin/mail-setting') ? 'active' : '' }}">
                <i class="nav-icon fas fa-envelope"></i>
                <p>
                    Mail Setting
                </p>
            </a>
        </li> --}}
            {{-- Home page Manage --}}
                <li
                    class="nav-item {{ Request::is('admin/page-settings/hero') || Request::is('admin/page-settings/sections-heading') || Request::is('admin/testimonial') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ Request::is('admin/page-settings/hero') || Request::is('admin/page-settings/sections-heading') || Request::is('admin/testimonial') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-igloo"></i>
                        <p>
                            Home Page Manage
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" @if (Request::is('admin/page-settings/hero') ||
                            Request::is('admin/page-settings/sections-heading') ||
                            Request::is('admin/sliders')) style="display: block;" @endif>
                            
                        <li class="nav-item ">
                            <a href="{{ url('admin/sliders') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sliders</p>
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a href="{{ url('admin/page-settings/hero') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hero Section</p>
                            </a>
                        </li>

                        <!-- <li class="nav-item ">
                            <a href="{{ url('admin/page-settings/sections-heading') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sections Heading</p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item ">
                            <a href="{{ url('admin/testimonial') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Testimonials</p>
                            </a>
                        </li> -->
                    </ul>
                </li>
                {{-- Home page Manage --}}

                 <li class="nav-item ">
					<a href="{{url('/admin/currency')}}" class="nav-link {{ Request::is('admin/currency*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Currency
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{url('/admin/payment-gateways')}}" class="nav-link {{ Request::is('admin/payment-gateways*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Payment Gateways
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li> 
				<li class="nav-item ">
					<a href="{{url('/admin/commission/global')}}" class="nav-link {{ Request::is('admin/commission/global*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Global Commission
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{url('/admin/vendor-commissions')}}" class="nav-link {{ Request::is('admin/vendor-commissions*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Vendor Commissions
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{url('/admin/product-commissions')}}" class="nav-link {{ Request::is('admin/product-commissions*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Product Commissions
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{url('/admin/transactions')}}" class="nav-link {{ Request::is('admin/transactions*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Transactions
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li> 
				<li class="nav-item ">
					<a href="{{url('/admin/partner-campaigns')}}" class="nav-link {{ Request::is('admin/partner-campaigns*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Partner Campaigns
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{url('/admin/partner-products')}}" class="nav-link {{ Request::is('admin/partner-products*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Partner Products
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<!--<li class="nav-item ">
					<a href="{{url('/admin/languages')}}" class="nav-link {{ Request::is('admin/languages*') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Languages
						  
						</p>
					</a>
				</li>-->
                      
        <li class="nav-item ">
            <a href="{{ url('admin/theme-setting') }}"
                class="nav-link {{ Request::is('admin/theme-setting') ? 'active' : '' }}">
                <i class="nav-icon fas fa-paint-brush"></i>
                <p>
                    Theme Setting

                </p>
            </a>

        </li>
      <li class="nav-item ">
        <a href="{{url('/admin/setting')}}" class="nav-link {{ Request::is('admin/setting') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-cog"></i>
          <p>
            Profile
          </p>
        </a>
      </li>
 
          <li class="nav-item ">

<a href="{{url('/admin/change-password')}}" class="nav-link {{ Request::is('admin/change-password') ? 'active' : '' }}">

<i class="nav-icon fas fa-key"></i>

<p>

Change Password



</p>

</a>


</li>


          <li class="nav-item ">

<a href="{{ url('admin/logout') }}" class="nav-link"   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
<i class="nav-icon fas fa-sign-out-alt"></i>

<p>

Logout



</p>

</a>


</li>

    </ul>

  </nav>

  <!-- /.sidebar-menu -->

</div>

<!-- /.sidebar -->

</aside>
<aside class="main-sidebar sidebar-light-primary elevation-4 admin-dashboard-sidebar">
	<!-- Brand Logo -->
	<a href="{{ url('/vendor/dashboard') }}" class="brand-link">
	    <img src="{{ asset('admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
	    <span class="brand-text font-weight-light">@if(auth()->check()) {{ Auth::user()->name }} @else Testing @endif</span>
	</a>
	<!-- Sidebar -->
	<div class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
			    @if(auth()->check())
					<img src="{{ asset('public/uploads/users') }}/{{ Auth::user()->image }}" class="img-circle elevation-2" alt="User Image">
				@else
			       <img src="{{ asset('admin/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
				@endif
			</div>
			<div class="info">
			  <a href="{{ url('/vendor/dashboard') }}" class="d-block">@if(auth()->check()) {{ Auth::user()->name }} @else Testing @endif</a>
			</div>
		</div>
		<!-- Sidebar Menu -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<li class="nav-item menu-open">
					<a href="{{ url('/vendor/dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
					  <i class="nav-icon fas fa-tachometer-alt"></i>
					  <p>
						Dashboard
					  </p>
					</a>
				</li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/category') }}"
                        class="nav-link {{ Request::is('vendor/categories*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Category

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/category-store') }}"
                        class="nav-link {{ Request::is('vendor/category-store') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                             Category Store

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/store') }}"
                        class="nav-link {{ Request::is('vendor/store') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Store

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/vendor-coupon') }}"
                        class="nav-link {{ Request::is('/vendor/vendor-coupon*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Coupon

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/wholesale2b/products/import') }}"
                        class="nav-link {{ Request::is('wholesale2b/products/import*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Import Products

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/products') }}"
                        class="nav-link {{ Request::is('vendor/products*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Products

                        </p>
                    </a>
                </li>
				
				<li class="nav-item ">
                    <a href="{{ url('/vendor/attributes') }}"
                        class="nav-link {{ Request::is('vendor/attributes*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Attributes
                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/upload-images') }}"
                        class="nav-link {{ Request::is('vendor/upload-images*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Upload Product Images

                        </p>
                    </a>
                </li> 
				<li class="nav-item ">
                    <a href="{{ url('/vendor/partner-products') }}"
                        class="nav-link {{ Request::is('vendor/partner-products*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Partner Products
                        </p>
                    </a>
                </li>
				
				<!--<li class="nav-item ">
                    <a href="{{ url('/vendor/product-brands') }}"
                        class="nav-link {{ Request::is('vendor/brands*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            Brand
                        </p>
                    </a>
               </li>-->
			   <li class="nav-item ">
                    <a href="{{ url('/vendor/product-orders') }}"
                        class="nav-link {{ Request::is('vendor/product-orders*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Orders

                        </p>
                    </a>
                </li>
				<li class="nav-item ">
                    <a href="{{ url('/vendor/partner-product-orders') }}"
                        class="nav-link {{ Request::is('vendor/partner-product-orders*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Campgain Orders

                        </p>
                    </a>
                </li>
				<!--<li class="nav-item ">
					<a href="{{url('/vendor/currency')}}" class="nav-link {{ Request::is('vendor/currency') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Currency
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{url('/vendor/vendor-languages')}}" class="nav-link {{ Request::is('vendor/vendor-languages') ? 'active' : '' }}">
						<i class="nav-icon fas fa-coins"></i>
						<p>
						  Languages
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>-->
				<li class="nav-item ">
					<a href="{{url('/vendor/change-password')}}" class="nav-link {{ Request::is('vendor/change-password') ? 'active' : '' }}">
						<i class="nav-icon fas fa-key"></i>
						<p>
						  Change Password
						  <i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
				<li class="nav-item ">
					<a href="{{ url('vendor/logout') }}" class="nav-link"   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
						<i class="nav-icon fas fa-sign-out-alt"></i>
						<p>
							Logout
							<i class="right fas fa-angle-left"></i>
						</p>
					</a>
				</li>
			</ul>
		</nav>
	  <!-- /.sidebar-menu -->
	  </div>
	 <!-- /.sidebar -->
	</aside>
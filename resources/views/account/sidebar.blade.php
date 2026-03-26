<ul class="nav nav-tabs mb-6" role="tablist">
	<li class="nav-item">
		<a href="{{url('/my-account')}}" class="nav-link-user {{ Request::is('my-account') ? 'active' : '' }}">Dashboard</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/wallet')}}" class="nav-link-user {{ Request::is('wallet') ? 'active' : '' }} {{ Request::is('wallet/add') ? 'active' : '' }}">My Wallet</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/orders')}}" class="nav-link-user {{ Request::is('account/orders') ? 'active' : '' }}">Orders</a>
	</li>
	{{-- <li class="nav-item">
		<a href="{{url('/account/partner-orders')}}" class="nav-link-user {{ Request::is('account/partner-orders') ? 'active' : '' }}">My Campaign Orders</a>
	</li> --}}
	<li class="nav-item">
		<a href="{{url('/account/downloads')}}" class="nav-link-user {{ Request::is('account/downloads') ? 'active' : '' }}">Downloads</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/address')}}" class="nav-link-user {{ Request::is('account/address') ? 'active' : '' }}">Addresses</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/details')}}" class="nav-link-user {{ Request::is('account/details') ? 'active' : '' }}">Account details</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/compare')}}" class="nav-link-user {{ Request::is('wishlist') ? 'active' : '' }}">Compare Product</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/wishlist')}}" class="nav-link-user {{ Request::is('account/wishlist') ? 'active' : '' }}">Wishlist</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/search-history')}}" class="nav-link-user {{ Request::is('account/search-history') ? 'active' : '' }}">Recent Search History</a>
	</li>
	{{-- <li class="nav-item">
		<a href="{{url('/account/link-accounts')}}" class="nav-link-user {{ Request::is('account/link-accounts') ? 'active' : '' }}">Linked Accounts</a>
	</li>
	 <li class="nav-item">
		<a href="{{url('/account/change-password')}}" class="nav-link-user {{ Request::is('account/change-password') ? 'active' : '' }}">Change Password</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/deactivate-account')}}" class="nav-link-user {{ Request::is('account/deactivate-account') ? 'active' : '' }}" style="color:red;" onclick="return deactivateValidate(this);">Deactivate Account</a>
	</li>
	<li class="nav-item">
		<a href="{{url('/account/delete-account-permanently')}}" onclick="return DeleteAccountPermanentlyValidate(this);" class="nav-link-user {{ Request::is('account/delete-account-permanently') ? 'active' : '' }}" style="color:red;">Delete Account Permanently</a>
	</li>
	<li class="nav-item">
		<a href="#" class="nav-link"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
		<form id="logout-form" action="{{ route('logout') }}" style="color:red;"  method="POST" style="display: none;">
			@csrf
		</form>
	</li> --}}
</ul>
       
<script>
function deactivateValidate(value){
	var con = confirm('Do you want to deactivate your account ?.');
	if(!con){
		return false;
	}
}
function DeleteAccountPermanentlyValidate(value){
	var con = confirm('Do you want to delete permanently your account ?.');
	if(!con){
		return false;
	}
}
</script>
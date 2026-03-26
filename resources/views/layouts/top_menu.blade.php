	<nav class="main-nav">
		<ul class="menu">
			<li class="{{ Request::is('/') ? 'active' : '' }}">
				<a href="{{url('/')}}">Home</a>
			</li>
			<li class="{{ Request::is('shop') ? 'active' : '' }}">
				<a href="{{url('/shop')}}">Shop</a>
			</li>
			<li class="{{ Request::is('stores') ? 'active' : '' }} {{ Request::is('vendor') ? 'active' : '' }}">
				<a href="{{url('/stores')}}">Stores</a>
			</li>
			<li class="{{ Request::is('my-community-deal') ? 'active' : '' }}">
			   <a href="{{url('/my-community-deal')}}">My Community deal</a>
			</li>
			<li class="{{ Request::is('commodities') ? 'active' : '' }} {{ Request::is('agricultural-commodities') ? 'active' : '' }} {{ Request::is('minerals-materials') ? 'active' : '' }}">
				<a href="{{url('/commodities')}}">Commodities</a>
				<ul>
					<li>
						<a href="{{url('/agricultural-commodities')}}">Agricultural Commodities</a>
					</li>
					<li>
						<a href="{{url('/minerals-materials')}}">Minerals & Materials</a>
					</li>
				</ul>
			</li>
		</ul>
	</nav>
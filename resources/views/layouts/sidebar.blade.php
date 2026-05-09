@php
$currentRoute = request()->route()->getName();
@endphp
<!--  BEGIN SIDEBAR  -->
<div class="sidebar-wrapper sidebar-theme">

<nav id="sidebar">

	<div class="navbar-nav theme-brand flex-row  text-center">
		<div class="nav-logo">
			<div class="nav-item theme-logo">
				<a href="./index.html">
					<img src="../src/assets/img/logo.svg" class="navbar-logo" alt="logo">
				</a>
			</div>
			<div class="nav-item theme-text">
				<a href="./index.html" class="nav-link"> CORK </a>
			</div>
		</div>
		<div class="nav-item sidebar-toggle">
			<div class="btn-toggle sidebarCollapse">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
			</div>
		</div>
	</div>
	<div class="shadow-bottom"></div>
	<ul class="list-unstyled menu-categories" id="accordionExample">
		<li class="menu {{ request()->routeIs('home') ? 'active' : '' }}">
			<a href="{{ route('home') }}" aria-expanded="false" class="dropdown-toggle">
				<div class="">
					<svg xmlns="http://www.w3.org/2000/svg" 
						viewBox="0 0 24 24" width="24" height="24" 
						fill="none" stroke="currentColor" stroke-width="2" 
						stroke-linecap="round" stroke-linejoin="round" 
						class="feather feather-home">
						<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
						<polyline points="9,22 9,12 15,12 15,22"></polyline>
					</svg>
					<span>Home</span>
				</div>
			</a>
		</li>

		<li class="menu {{ request()->routeIs('setup') ? 'active' : '' }}">
			<a href="{{ route('setup.edit', 1) }}" aria-expanded="false" class="dropdown-toggle">
				<div class="">
					<svg xmlns="http://www.w3.org/2000/svg" 
						viewBox="0 0 24 24" width="24" height="24" 
						fill="none" stroke="currentColor" stroke-width="2" 
						stroke-linecap="round" stroke-linejoin="round" 
						class="feather feather-settings">
						<circle cx="12" cy="12" r="3"></circle>
						<path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09c.7 0 1.31-.4 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06c.51.51 1.22.66 1.82.33.47-.25 1-.8 1-1.51V3a2 2 0 1 1 4 0v.09c0 .7.4 1.31 1 1.51.6.33 1.31.18 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06c-.51.51-.66 1.22-.33 1.82.25.47.8 1 1.51 1H21a2 2 0 1 1 0 4h-.09c-.7 0-1.31.4-1.51 1z"></path>
					</svg>
					<span>Setup</span>
				</div>
			</a>
		</li>

		<li class="menu {{ request()->routeIs('category') ? 'active' : '' }}">
			<a href="{{ route('category.index') }}" aria-expanded="false" class="dropdown-toggle">
				<div class="">
					<svg xmlns="http://www.w3.org/2000/svg" 
						viewBox="0 0 24 24" width="24" height="24" 
						fill="none" stroke="currentColor" stroke-width="2" 
						stroke-linecap="round" stroke-linejoin="round" 
						class="feather feather-layers">
						<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
						<polyline points="2 17 12 22 22 17"></polyline>
						<polyline points="2 12 12 17 22 12"></polyline>
					</svg>
					<span>Category</span>
				</div>
			</a>
		</li>

		<li class="menu {{ request()->routeIs('project') ? 'active' : '' }}">
			<a href="{{ route('project.index') }}" aria-expanded="false" class="dropdown-toggle">
				<div class="">
					<svg xmlns="http://www.w3.org/2000/svg" 
						viewBox="0 0 24 24" width="24" height="24" 
						fill="none" stroke="currentColor" stroke-width="2" 
						stroke-linecap="round" stroke-linejoin="round" 
						class="feather feather-briefcase">
						<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
						<path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"></path>
					</svg>
					<span>Project</span>
				</div>
			</a>
		</li>
	</ul>
	
</nav>

</div>
<!--  END SIDEBAR  -->
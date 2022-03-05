@include('header')
<div class="container">
<div class="row">

<div class="col-7">
</br>
<div class="bs-callout bs-callout-primary">
        <h2>{{$title}}</h2>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-4 col-lg-3">
        <nav id="nav-custom-menu" class="sidebar bg-white">
            <div class="position-sticky">
                <div class="list-group list-group-flush mx-3 mt-4">
                    <a href="{{url('my-account')}}" class="list-group-item list-group-item-action py-2 ripple text-start" aria-current="true">
                    <span>Dashboard</span>
                    </a>
                    <a href="{{url('child-donation')}}" class="list-group-item list-group-item-action py-2 ripple text-start" aria-current="true">
                    <span>List Anak</span>
                    </a>
                    <a href="{{url('project-donation')}}" class="list-group-item list-group-item-action py-2 ripple text-start" aria-current="true">
                    <span>List Proyek</span>
                    </a>
                    <a href="{{url('edit-account')}}" class="list-group-item list-group-item-action py-2 ripple text-start" aria-current="true">
                    <span>Edit Account</span>
                    </a>
                    <form id="logout-form" action="{{ route('sponsor.logout') }}" method="POST" style="display: none;">
                            @csrf
                    </form>  
                    <a href="{{route('sponsor.logout')}}" class="list-group-item list-group-item-action py-2 ripple text-start" aria-current="true"
                    onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                    <span>Logout</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
    <div class="col-lg-9 col-md-8">
    </br>
    @yield('content')
    </div>
</div>
</div>
@include('footer')
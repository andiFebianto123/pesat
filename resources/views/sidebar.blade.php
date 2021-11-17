@include('header')
<div class="container">
<div class="row">

<div class="col-7">
</br>
<div class="bs-callout bs-callout-primary">
        <h2>Akun Saya</h2>
    </div>
</div>
</div>
<div class="row">
    <div class="col-1">
        <nav id="nav-custom-menu" class="collapse d-lg-block sidebar collapse bg-white">
            <div class="position-sticky">
                <div class="list-group list-group-flush mx-3 mt-4">
                    <a href="{{url('my-account')}}" class="list-group-item list-group-item-action py-2 ripple" aria-current="true">
                    <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Dashboard</span>
                    </a>
                    <a href="{{url('child-donation')}}" class="list-group-item list-group-item-action py-2 ripple" aria-current="true">
                    <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Child Donation</span>
                    </a>
                    <a href="{{url('project-donation')}}" class="list-group-item list-group-item-action py-2 ripple" aria-current="true">
                    <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Project Donation</span>
                    </a>
                    <a href="{{url('edit-account')}}" class="list-group-item list-group-item-action py-2 ripple" aria-current="true">
                    <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Edit Account</span>
                    </a>
                    <form id="logout-form" action="{{ route('sponsor.logout') }}" method="POST" style="display: none;">
                            @csrf
                    </form>  
                    <a href="{{route('sponsor.logout')}}" class="list-group-item list-group-item-action py-2 ripple" aria-current="true"
                    onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                    <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Logout</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>


    <div class="col-2">
    </div>
    <div class="col-7">
    </br>
    @yield('content')
    </div>
</div>
</div>
@include('footer')
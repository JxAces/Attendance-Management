@extends('layouts.user_type.guest')

@section('content')

<main class="main-content mt-0">
    <section>
        <div class="page-header d-flex justify-content-center align-items-center min-vh-75">
            <div class="d-flex flex-column align-items-center" style="height: 700px;">
                <img src="{{ asset('assets/img/ccs.jpg') }}" alt="..." class="col-md-2" style> 
                <h3>Attendance Monitoring System</h3>
                <div class="card-body bg-white shadow rounded p-4 mt-4" style="width: 100%; height: 100%; max-width: 600px;"> 
                    <form role="form" method="POST" action="/session">
                        @csrf
                            <label for="email">Email</label>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="" aria-label="Email" aria-describedby="email-addon">
                                @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <label for="password">Password</label>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="" aria-label="Password" aria-describedby="password-addon">
                                @error('password')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">Sign in</button>
                            </div>
                            <div></div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1 mt-3">
                        <small class="text-muted">
                            <a href="/login/forgot-password" class="text-info text-gradient font-weight-bold">Forgot your password? Reset your password here</a>
                        </small>
                        <p class="mb-4 text-sm mx-auto">
                            <a href="register" class="text-info text-gradient font-weight-bold">Sign up</a>
                        </p>
                        </div>
                    </form>
                </div>
            </div>           
        </div>           
    </section>
</main>


@endsection

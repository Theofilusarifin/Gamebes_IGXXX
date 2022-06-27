
@extends('auth.layouts.app')

@section('content')
<main>
    <section class="vh-lg-100 mt-5 mt-lg-0 bg-soft d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center form-bg-image">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="bg-white shadow border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                        <div class="text-center text-md-center mb-4 mt-md-0">
                            <h1 class="mb-4 h3">Industrial Games XXX <br> Login Form</h1>
                        </div>
                        <form method="POST" action="{{ route('login') }}" class="mt-4">
                            @csrf
                            <!-- Form -->
                            <div class="form-group mb-4">
                                <label for="username">Username</label>
                                <div class="input-group">
                                    <input type="text" name="username" class="form-control"
                                        placeholder="username" id="username" autofocus required>
                                </div>
                            </div>
                            <!-- End of Form -->
                            <div class="form-group">
                                <!-- Form -->
                                <div class="form-group mb-4">
                                    <label for="password">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" placeholder="Password"
                                            class="form-control" id="password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-gray-800">Login</button>
                            </div>
                        </form>

                        @foreach($errors->all() as $error)
                        <p class="text-danger">{{ $error }}</p>
                        @endforeach

                        {{-- <div class="d-flex justify-content-center align-items-center mt-4">
                            <span class="fw-normal">
                                Not registered?
                                <a href="register" class="fw-bold">Create account</a>
                            </span>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
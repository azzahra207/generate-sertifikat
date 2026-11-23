@extends('layouts.app')

@section('content')
<style>
    *{
        color:#ffffff;
    }
     @media only screen and (min-width: 1001px){
        .d-grid{
            grid-template-columns: repeat(2, 1fr);
            height:auto;

        }
        #left{
            width:50%;
            padding:auto;
        }
        #gmb{
            margin:auto;
        }
    }
    #left{
           background-color: #1E90FF;
           width:100%;
        }
     @media only screen and (max-width:1000px){
        .d-grid{
            grid-template-columns: repeat(1,1fr);  
            width: 100%;
            height:auto;     
        }
            #gmb{
               width: 100%
            }
     }
</style>
    <div class="d-grid">
        <div id="right" class="d-flex justify-content-center">
            <img class="mx-auto" id="gmb" src="img/register.png" alt="">
        </div>
        <div id="left" class="p-5 m-auto">
           
                    <h2 class="text-center fw-bold mb-3">{{ __('Create Free Account') }}</h2>
                    
                    
                    <div class="card-body">
                      <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="row mb-3">
                        <label for="name" class="col-form-label text-md-start">{{ __('Name') }}</label>
                        <div>
                            <input placeholder="Otong Surotong" id="name" type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    {{-- Username --}}
                    <div class="row mb-3">
                        <label for="username" class="col-form-label text-md-start">{{ __('Username') }}</label>
                        <div>
                            <input placeholder="otong_surotong" id="username" type="text"
                                   class="form-control @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username') }}" required autocomplete="username">
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                
                    {{-- Email (BARU) --}}
                    <div class="row mb-3">
                        <label for="email" class="col-form-label text-md-start">{{ __('Email Address') }}</label>
                        <div>
                            <input placeholder="otong@example.com" id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                
                    {{-- Password --}}
                    <div class="row mb-3">
                        <label for="password" class="col-form-label text-md-start">{{ __('Password') }}</label>
                        <div>
                            <input placeholder="********" id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                
                    {{-- Confirm Password --}}
                    <div class="row mb-3">
                        <label for="password-confirm" class="col-form-label text-md-start">{{ __('Confirm Password') }}</label>
                        <div>
                            <input placeholder="********" id="password-confirm" type="password"
                                   class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                
                    {{-- Tombol --}}
                    <div class="row mb-3">
                        <div>
                            <button class="btn btn-danger" type="reset">Hapus</button>
                            <button type="submit" class="btn btn-light" style="color: #1E90FF">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
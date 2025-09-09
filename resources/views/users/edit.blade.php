@extends('layouts.app')

@section('content')
    <div class="container-body">
        <h1>Редактировать профиль</h1>

        @if($user)
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="btn-primary">Сохранить изменения</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('profile') }}'">Отмена</button>
                </div>
            </form>
        @else
            <p>Пользователь не найден.</p>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            color: #333;
        }

        .container-body {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .alert .close {
            cursor: pointer;
            float: right;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 15px;
            justify-content: space-between;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="email"] {
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 80%;
            padding: 10px;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus {
            border-color: #80bdff;
            outline: none;
        }

        .button-wrapper {
            display: flex;
            justify-content: space-between;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button.btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        button.btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
@endpush

@extends('mail.base')
    @section('title', 'Completa la registrazione')

    @section('body')
        <div class="px-5">
            <h1>Completa la registrazione</h1>
            <p>Hai ricevuto questa e-mail in seguito alla registrazione da parte di un collaboratore Zanichelli.</p>

            <p class="mb-0">Di seguito trovi i tuoi dati.</p>
            <div>
                <div class="d-flex align-items-center data-row">
                    <div>
                        <p class="text-bold mb-0">Username</p>
                    </div>
                    <div>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="d-flex align-items-center data-row">
                    <div>
                        <p class="text-bold mb-0">Nome</p>
                    </div>
                    <div>
                        <p class="mb-0">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="d-flex align-items-center data-row">
                    <div>
                        <p class="text-bold mb-0">Cognome</p>
                    </div>
                    <div>
                        <p class="mb-0">{{ $user->surname }}</p>
                    </div>
                </div>
            </div>

            <p>Se non hai richiesto tu la registrazione ignora questa e-mail, altrimenti clicca sul bottone sottostante per completare la registrazione.</p>

            <a href="{{route('complete-registration', ['token' => $token])}}" role="button"
               class="btn btn-primary mt-4 mb-4">Completa la registrazione</a>
            <p class="text-muted">Se il bottone non dovesse funzionare, copia e incolla sul tuo browser il seguente link:</p>
            <small>{{route('complete-registration', ['token' => $token])}}</small>
        </div>
    @endsection
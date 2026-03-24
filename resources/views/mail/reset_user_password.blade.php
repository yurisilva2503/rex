<x-mail::message>
# {{ config('app.name') }} - Confirmação de Criação de Conta

Olá, {{ $name ?? 'Usuário' }}.

Sua solicitação de redefinição de senha em {{ config('app.name') }} foi criada com sucesso.

Para criar uma nova senha, clique no botão abaixo:

<x-mail::button :url="$resetLink ?? '#'">
Redefinir Senha
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}

© {{ date('Y') }} - Todos os direitos reservados.
</x-mail::message>

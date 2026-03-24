<x-mail::message>
# {{ env('APP_NAME') }} - Confirmação de Criação de Conta

Olá, {{ $name ?? 'Usuário' }}.

Sua conta em **{{ env('APP_NAME') }}** foi criada com sucesso.

Para confirmar sua conta, clique no botão abaixo:

<x-mail::button :url="$confirmationLink ?? '#'">
Confirmar Conta
</x-mail::button>

Se você não criou essa conta, nenhuma ação é necessária.

Obrigado,<br>
{{ env('APP_NAME') }}

© {{ date('Y') }} - Todos os direitos reservados.
</x-mail::message>

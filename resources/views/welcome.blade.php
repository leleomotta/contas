<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Controle Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.pexels.com/photos/3194521/pexels-photo-3194521.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="bg-opacity-50">
<div class="container mx-auto p-4">
    <div class="bg-white rounded p-8 shadow-md max-w-md mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-6 text-center">Controle Financeiro</h1>
        <p class="text-gray-700 mb-8 text-center">Gerencie suas finanças com inteligência e alcance seus objetivos.</p>

        <div class="flex justify-center space-x-4">
            <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Login</a>
            <a href="" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Registrar</a>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ url('/home') }}" class="text-blue-500 hover:underline">Ir para o Início</a>
        </div>
    </div>
</div>
</body>
</html>



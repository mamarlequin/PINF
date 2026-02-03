<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
  header("Location:../index.php");
  die("");
}

// Pose qq soucis avec certains serveurs...
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FabLab</title>


    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="shortcut icon" href="./img/logo.png">
    
    <link rel="shortcut icon" href="./img/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="./css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }

    </style>
</head>
<body class="text-slate-800 mx-20">
    <nav class="max-w-6xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 sticky top-4 my-10 z-50">
        <div class="bg-slate-300 rounded-full border shadow-sm px-6 flex items-center py-2 justify-between h-1-">
                <a href="index.php?view=main" style="margin-right:20px;">
                    <img 
                    src="./img/logo.png"
                    alt="logo du Fablab"
                    width="85"                
                    />
                </a> 

                
                
                <div class="hidden md:flex items-center gap-14 text-sm-bold font-medium ">
                    <a href="./pages/reserver.php" class="transition-colors hover:text-indigo-600">Réserver</a>
                    <a href="./pages/machines.php" class="transition-colors hover:text-indigo-600">Machines</a>
                    
                    <a href="./pages/login.php" class="bg-indigo-600 text-white px-5 py-2 rounded-3xl hover:bg-indigo-700 transition-all shadow-sm active:scale-95">
                        Connexion
                    </a>
                </div>
        </div>
    </nav>
<?php
// app/helpers.php · Funciones de ayuda que usan las vistas

// Escapa el texto antes de mostrarlo en HTML (evita ataques XSS)
function e(mixed $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Da formato de quetzales: 1234.5 → Q1,234.50
function quetzales(float|string|null $monto): string
{
    return 'Q' . number_format((float) $monto, 2);
}

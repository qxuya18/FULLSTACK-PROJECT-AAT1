-- 05_agrupar.sql · AHD2 · Funciones de agregado, GROUP BY y HAVING
USE papeleria;

-- 1. Funciones de agregado: resumen de TODA la tabla en una sola fila
SELECT
  COUNT(*)              AS total_productos,
  SUM(stock)            AS unidades_en_bodega,
  ROUND(AVG(precio), 2) AS precio_promedio,
  MIN(precio)           AS mas_barato,
  MAX(precio)           AS mas_caro
FROM productos;

-- 2. GROUP BY: cuántos productos hay en cada categoría
SELECT id_categoria, COUNT(*) AS cantidad_productos
FROM productos
GROUP BY id_categoria;

-- 3. Total vendido (en quetzales) de cada venta
SELECT id_venta,
       SUM(cantidad * precio_unitario) AS total
FROM detalle_ventas
GROUP BY id_venta;

-- 4. HAVING: filtra GRUPOS (WHERE filtra filas)
--    Ventas cuyo total supera Q100
SELECT id_venta,
       SUM(cantidad * precio_unitario) AS total
FROM detalle_ventas
GROUP BY id_venta
HAVING total > 100
ORDER BY total DESC;

-- 5. Unidades vendidas por producto, de la más vendida a la menos vendida
SELECT id_producto, SUM(cantidad) AS unidades_vendidas
FROM detalle_ventas
GROUP BY id_producto
ORDER BY unidades_vendidas DESC;

-- 6. Funciones de texto y fecha
SELECT UPPER(nombre)                     AS cliente,
       CONCAT('NIT: ', nit)              AS nit,
       LENGTH(nombre)                    AS letras
FROM clientes;

SELECT id_venta,
       DATE_FORMAT(fecha, '%d/%m/%Y')    AS dia,
       DATE_FORMAT(fecha, '%H:%i')       AS hora,
       DATEDIFF('2026-10-21', fecha)     AS dias_transcurridos
FROM ventas;

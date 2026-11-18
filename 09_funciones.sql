-- 09_funciones.sql · AHD2 · Funciones almacenadas
USE papeleria;

-- DELIMITER cambia el "fin de instrucción" de ; a $$
-- para que MySQL no corte la función en el primer ;
DELIMITER $$

-- 1. Devuelve el total en quetzales de una venta
DROP FUNCTION IF EXISTS fn_total_venta $$
CREATE FUNCTION fn_total_venta(p_id_venta INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  DECLARE v_total DECIMAL(10,2);

  SELECT IFNULL(SUM(cantidad * precio_unitario), 0)
  INTO v_total
  FROM detalle_ventas
  WHERE id_venta = p_id_venta;

  RETURN v_total;
END $$

-- 2. Recibe un stock y devuelve un texto con su estado
DROP FUNCTION IF EXISTS fn_estado_stock $$
CREATE FUNCTION fn_estado_stock(p_stock INT)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
  IF p_stock = 0 THEN
    RETURN 'Agotado';
  ELSEIF p_stock < 15 THEN
    RETURN 'Bajo';
  ELSE
    RETURN 'Suficiente';
  END IF;
END $$

-- 3. Aplica un descuento (%) a un precio
DROP FUNCTION IF EXISTS fn_precio_descuento $$
CREATE FUNCTION fn_precio_descuento(p_precio DECIMAL(8,2), p_porcentaje INT)
RETURNS DECIMAL(8,2)
DETERMINISTIC
BEGIN
  RETURN ROUND(p_precio - (p_precio * p_porcentaje / 100), 2);
END $$

DELIMITER ;

-- Usar las funciones como si fueran de MySQL (SUM, ROUND...)
SELECT fn_total_venta(2) AS total_venta_2;

SELECT id_venta, fecha, fn_total_venta(id_venta) AS total
FROM ventas;

SELECT nombre, stock, fn_estado_stock(stock) AS estado
FROM productos
ORDER BY stock;

SELECT nombre, precio,
       fn_precio_descuento(precio, 15) AS precio_con_15
FROM productos
WHERE id_categoria = 1;

-- 11_transacciones.sql · AHD2 · Control de transacciones (TCL)
-- ⚠️ En phpMyAdmin ejecuta CADA EJEMPLO completo en una sola vez (un solo "Continuar").
USE papeleria;

-- ===== Ejemplo A · ROLLBACK: deshacer un error =====
START TRANSACTION;

  -- ¡Ups! Un DELETE sin WHERE borra TODO el detalle
  DELETE FROM detalle_ventas;
  SELECT COUNT(*) AS lineas_dentro_de_la_transaccion FROM detalle_ventas;

ROLLBACK;   -- deshace todo desde START TRANSACTION

SELECT COUNT(*) AS lineas_despues_del_rollback FROM detalle_ventas;


-- ===== Ejemplo B · COMMIT: registrar una venta completa =====
--   Son 4 operaciones que deben ocurrir TODAS o NINGUNA
START TRANSACTION;

  INSERT INTO ventas (fecha, id_cliente, id_empleado)
  VALUES ('2026-10-21 10:15:00', 3, 2);
  SET @v = LAST_INSERT_ID();

  INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario)
  VALUES (@v, 2, 2, 15.00),     -- 2 cuadernos de dibujo
         (@v, 6, 1, 24.20);     -- 1 caja de crayones

  UPDATE productos SET stock = stock - 2 WHERE id_producto = 2;
  UPDATE productos SET stock = stock - 1 WHERE id_producto = 6;

COMMIT;     -- confirma: los cambios quedan guardados para siempre

SELECT * FROM v_total_ventas WHERE id_venta = @v;
SELECT nombre, stock FROM productos WHERE id_producto IN (2, 6);


-- ===== Ejemplo C · SAVEPOINT: deshacer solo una parte =====
START TRANSACTION;

  UPDATE productos SET precio = 20.00 WHERE codigo = 'CUA-001';
  SAVEPOINT despues_del_cuaderno;

  UPDATE productos SET precio = 1.00 WHERE codigo = 'TEC-002';  -- error de dedo
  ROLLBACK TO SAVEPOINT despues_del_cuaderno;  -- deshace solo la calculadora

COMMIT;

SELECT codigo, nombre, precio FROM productos WHERE codigo IN ('CUA-001', 'TEC-002');

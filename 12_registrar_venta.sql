-- 12_registrar_venta.sql · AHD2 · Procedimiento con transacción
USE papeleria;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_registrar_venta $$
CREATE PROCEDURE sp_registrar_venta(
  IN  p_id_cliente  INT,
  IN  p_id_empleado INT,
  IN  p_id_producto INT,
  IN  p_cantidad    INT,
  OUT p_id_venta    INT
)
BEGIN
  DECLARE v_stock  INT;
  DECLARE v_precio DECIMAL(8,2);

  -- Si CUALQUIER instrucción falla: deshacer todo y avisar el error
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

    -- 1. Leer el stock y el precio (FOR UPDATE bloquea la fila
    --    para que otra caja no venda el mismo producto al mismo tiempo)
    SELECT stock, precio INTO v_stock, v_precio
    FROM productos
    WHERE id_producto = p_id_producto
    FOR UPDATE;

    -- 2. Validaciones del negocio
    IF v_stock IS NULL THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto no existe';
    END IF;

    IF p_cantidad > v_stock THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock';
    END IF;

    -- 3. Encabezado de la venta
    INSERT INTO ventas (fecha, id_cliente, id_empleado)
    VALUES (NOW(), p_id_cliente, p_id_empleado);
    SET p_id_venta = LAST_INSERT_ID();

    -- 4. Detalle con el precio actual del producto
    INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario)
    VALUES (p_id_venta, p_id_producto, p_cantidad, v_precio);

    -- 5. Descontar del inventario
    UPDATE productos
    SET stock = stock - p_cantidad
    WHERE id_producto = p_id_producto;

  COMMIT;
END $$

DELIMITER ;

-- Prueba 1 · Venta correcta: Librería La Esquina (7) compra 2 calculadoras (12)
CALL sp_registrar_venta(5,2,10,2, @nueva);
SELECT @nueva AS venta_creada;
SELECT * FROM v_detalle_ventas WHERE id_venta = @nueva;
SELECT nombre, stock FROM productos WHERE id_producto = 12;

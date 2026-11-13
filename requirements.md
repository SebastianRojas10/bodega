# Requirements Document

## Introduction

This document specifies the requirements for the sales registration system for the bodega "Doña Rosa" in San Vicente de Cañete. The system allows the store clerk to register products, record sales with volume discounts, and automatically update inventory stock after each sale. The system runs on a single computer with XAMPP (PHP 8 + MySQL) without authentication, reporting, or electronic invoicing features.

## Glossary

- **Sistema_Ventas**: The sales registration system for Doña Rosa's bodega
- **Producto**: An item available for sale with name, price, and stock quantity
- **Venta**: A sales transaction containing one or more product lines
- **Línea_Venta**: A single line item in a sale specifying product and quantity
- **Stock**: The available quantity of a product in inventory
- **Subtotal**: The sum of (price × quantity) for all lines in a sale before discount
- **Descuento**: A percentage reduction applied to the subtotal when threshold is exceeded
- **Cantidad**: The number of units of a product in a sale line
- **Transacción**: A database transaction ensuring atomicity of operations

## Requirements

### Requirement 1: Product Registration

**User Story:** Como encargada de la bodega, quiero registrar productos con nombre, precio y stock, para tener un catálogo actualizado de mercadería disponible.

#### Acceptance Criteria

1. THE Sistema_Ventas SHALL accept product name, unit price, and initial stock quantity
2. WHEN a product is registered, THE Sistema_Ventas SHALL store the product with name, price, and stock in the database
3. THE Sistema_Ventas SHALL accept unit prices greater than zero as decimal values
4. THE Sistema_Ventas SHALL accept initial stock quantities as non-negative integers

### Requirement 2: Stock Validation

**User Story:** Como encargada de la bodega, quiero que el sistema valide el stock disponible antes de confirmar una venta, para no vender productos que no tengo.

#### Acceptance Criteria

1. WHEN a sale line quantity exceeds the available stock for that product, THE Sistema_Ventas SHALL reject the sale (R1)
2. WHEN a sale line quantity equals the available stock for that product, THE Sistema_Ventas SHALL accept the sale and set stock to zero (R1)
3. WHEN a sale line quantity is zero, THE Sistema_Ventas SHALL reject the sale line (R3)
4. WHEN a sale line quantity is negative, THE Sistema_Ventas SHALL reject the sale line (R3)
5. WHEN a sale line quantity is a decimal number, THE Sistema_Ventas SHALL reject the sale line (R3)

### Requirement 3: Sales Registration

**User Story:** Como encargada de la bodega, quiero registrar ventas con una o varias líneas de productos, para llevar un control de lo que se vende cada día.

#### Acceptance Criteria

1. THE Sistema_Ventas SHALL accept one or more product lines in a single sale
2. WHEN a product line is added to a sale, THE Sistema_Ventas SHALL specify the product and quantity
3. THE Sistema_Ventas SHALL calculate the subtotal as the sum of (unit price × quantity) for all lines in the sale
4. THE Sistema_Ventas SHALL display the subtotal, discount, and final total before confirming the sale

### Requirement 4: Volume Discount Application

**User Story:** Como encargada de la bodega, quiero que el sistema calcule automáticamente el descuento por volumen, para no equivocarme al dar el precio final al cliente.

#### Acceptance Criteria

1. WHEN the subtotal is greater than S/ 100.00, THE Sistema_Ventas SHALL apply a 5% discount to the subtotal (R2)
2. WHEN the subtotal equals S/ 100.00, THE Sistema_Ventas SHALL apply zero discount (R2)
3. WHEN the subtotal is less than S/ 100.00, THE Sistema_Ventas SHALL apply zero discount (R2)
4. THE Sistema_Ventas SHALL calculate the final total as subtotal minus discount amount

### Requirement 5: Stock Update on Sale Confirmation

**User Story:** Como encargada de la bodega, quiero que el stock se actualice automáticamente al confirmar la venta, para que el inventario refleje lo que realmente me queda.

#### Acceptance Criteria

1. WHEN a sale is confirmed, THE Sistema_Ventas SHALL reduce the stock of each product by the quantity sold within the same database transaction (R4)
2. IF the stock update fails for any product line, THEN THE Sistema_Ventas SHALL roll back the entire sale transaction (R4)
3. WHEN the sale transaction completes successfully, THE Sistema_Ventas SHALL persist the updated stock values to the database (R4)

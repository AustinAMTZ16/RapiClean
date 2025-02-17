Servicios (ServicioID) ───┐
                          ├── Servicio_Insumos (ServicioID, ProductoID)
Inventario (ProductoID) ──┘                                                
                                                                           
                                                                        
Roles (RolID) ───┐                                                         
                 ├── RolesPermisos (RolID, PermisoID)                 
Permisos (PermisoID) ───┘                                                 
                                                                          
Usuarios (UsuarioID) ───┐                                                
                        ├── Roles (RolID)                 
                        ├── Descuentos (UsuarioID)                        
                        └── Ventas (UsuarioID)                            
                                                                          
Clientes (ClienteID) ───┐                                                 
                        ├── Ventas (ClienteID)                            
                        └── CanjesRecompensas (ClienteID)                 
Ventas (VentaID) ───┐                                                      
                    ├── SemaforoServicios (VentaID)| - SP CanjearRecompensa(CanjearRecompensa) Si PuntosRecompensa > PuntosNecesarios             
                    ├── Descuentos (DescuentoID)   | - SP ReducirInventario(ServicioID) Si EstadoPedido = Listo          
                    ├── Usuarios (UsuarioID)       | - SP AsignarPuntosRecompensa(p_ClienteID, p_TotalVenta) Si EstadoVenta = Pagado
                    └── Clientes (ClienteID)       | - SP AplicarDescuento (p_VentaID, p_CodigoPromocion) Si EstadoPedido = Registrado

Recompensas (RecompensaID) ───┐                                           
                              └── CanjesRecompensas (RecompensaID)

// Archivo: publicaciones/Tarjeta.js
import React from 'react';
import '../../HarryStyles/Tarjeta.css';

const Tarjeta = ({ publicacion }) => {
  return (
    <div className="tarjeta">
      <img src={publicacion.imagen} alt={publicacion.nombre} className="tarjeta-imagen" />
      <div className="tarjeta-detalles">
        <h2>{publicacion.nombre}</h2>
        <p>{publicacion.descripcion}</p>
        <p>Usuario: {publicacion.user}</p>
        <p>Categoría: {publicacion.categoria}</p>
        <p>Estado: {publicacion.estado}</p>
      </div>
    </div>
  );
}

export default Tarjeta;
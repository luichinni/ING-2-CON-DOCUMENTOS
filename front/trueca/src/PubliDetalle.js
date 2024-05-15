import React from 'react';

const PubliDetalle = ({ publicacion, onClose }) => {
  return (
<div className="fixed top-0 left-0 right-0 bottom-0 flex items-center justify-center bg-gray-800 bg-opacity-75 z-50">
  <div className="bg-white p-4 rounded-md w-96">
      <img src={publicacion.imagen} alt="Producto" className="w-56 h-48 mt-5 mx-auto" />
        <h1 className="text-xl font-bold capitalize mb-2 font-serif">{publicacion.titulo}</h1>
        <p className="text-sm mb-2 font-serif">
          <strong>Por:</strong> {publicacion.autor}
        </p>
        <p className="text-sm mb-2 font-serif">
          <strong>Categoría:</strong> {publicacion.categoria}
        </p>
        <p className="text-sm mb-2">
          <strong>Centros de preferencia:</strong> {publicacion.centro}
        </p>
        <p className="text-sm">{publicacion.descripcion}</p>
        <button onClick={onClose} className="bg-blue-700 px-2 rounded-md py-1 mt-1 hover:bg-blue-500">
          <span style={{ color: 'white' }}>Cerrar publicación</span>
        </button>
      </div>
    </div>
  );
}

export default PubliDetalle;
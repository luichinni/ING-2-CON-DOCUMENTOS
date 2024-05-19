import React, { useState } from 'react';
import PubliDetalle from './PubliDetalle';

const Tarjeta = ({ publicacion }) => {
  const [publicacionSeleccionada, setPublicacionSeleccionada] = useState(null);
  const [descripcionCompleta, setDescripcionCompleta] = useState(false);

  const abrirPublicacion = () => {
    setPublicacionSeleccionada(publicacion);
  };

  const cerrarPublicacion = () => {
    setPublicacionSeleccionada(null);
  };

  const truncarDescripcion = (texto, longitudMaxima) => {
    if (texto.length <= longitudMaxima) {
      return texto;
    }
    return texto.slice(0, longitudMaxima) + '...';
  };

  const truncarCentro = (texto, longitudMaxima) => {
    if (texto.length <= longitudMaxima) {
      return texto;
    }
    return texto.slice(0, longitudMaxima) + '...';
  };

  return (
    <div className="bg-gray-200 text-black p-2 rounded-md flex flex-col sm:flex-row">
      <div className="flex flex-col sm:flex-row items-center">
        <img src={publicacion.imagen} alt="Producto" className="flex-shrink-0 w-36 h-36 mt-5 mr-4" />
        <div className="flex flex-col flex-grow">
          <h1 className="text-xl font-bold capitalize mb-2 font-serif">{publicacion.titulo}</h1>
          <p className="text-sm mb-2 font-serif">
            <strong>Por:</strong> {publicacion.autor}
          </p>
          <p className="text-sm mb-2 font-serif">
            <strong>Categoría:</strong> {publicacion.categoria}
          </p>
          <p className="text-sm mb-2">
            <strong>Centros de preferencia:</strong> {truncarCentro(publicacion.centro, 20)}
          </p>
          <p className="text-sm">
            {descripcionCompleta ? publicacion.descripcion : truncarDescripcion(publicacion.descripcion, 34)}
            {!descripcionCompleta && publicacion.descripcion.length > 34 && (
              <button className="text-blue-500 hover:underline focus:outline-none" onClick={() => setDescripcionCompleta(true)}>
                Ver más
              </button>
            )}
          </p>
          {descripcionCompleta && (
            <div>
              <button className="text-blue-500 hover:underline focus:outline-none" onClick={() => setDescripcionCompleta(false)}>
                  Ver menos
              </button>
            </div>
          )}
          <div className="flex mt-2">
            <button
              onClick={abrirPublicacion}
              className="bg-blue-700 px-2 rounded-md py-1 mt-1 mr-2 hover:bg-blue-500"
            >
              <span style={{ color: 'white' }}>Ver publicación</span>
            </button>
          </div>
        </div>
      </div>
      {publicacionSeleccionada === publicacion && <PubliDetalle publicacion={publicacion} onClose={cerrarPublicacion} />}
    </div>
  );
};

export default Tarjeta;
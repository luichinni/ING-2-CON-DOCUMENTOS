import React from "react";

const PubliDetalle = () => {
  const publicacion = JSON.parse(localStorage.getItem("publicacion"));

  if (!publicacion) {
    return <div>La publicación no está disponible</div>;
  }

  return (
    <div className="publi-detalle">
      <div className="imagen-principal">
        <img src={`data:${publicacion.imagenes[0].tipo_imagen};base64,${publicacion.imagenes[0].archivo}`} alt={publicacion.nombre} />
      </div>
      <div className="detalles-publicacion">
        <h1>{publicacion.nombre}</h1>
        <p><strong>Descripción:</strong> {publicacion.descripcion}</p>
        <p><strong>Categoría:</strong> {publicacion.categoria}</p>
        <p><strong>Estado:</strong> {publicacion.estado}</p>
        <p><strong>Vendedor:</strong> {publicacion.user}</p>
      </div>
      <div className="imagenes-adicionales">
        <h2>Imágenes adicionales</h2>
        <div className="lista-imagenes">
          {publicacion.imagenes.slice(1).map((imagen, index) => (
            <img key={index} src={`data:${imagen.tipo_imagen};base64,${imagen.archivo}`} alt={`Imagen ${index + 2}`} />
          ))}
        </div>
      </div>
    </div>
  );
};

export default PubliDetalle;



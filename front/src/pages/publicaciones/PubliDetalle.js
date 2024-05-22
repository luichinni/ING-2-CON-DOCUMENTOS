import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import "../../HarryStyles/PubliDetalle.css"; // Asegúrate de crear este archivo CSS

const PubliDetalle = () => {
    const { id } = useParams(); // Extraer el parámetro id de la URL
    const [publicacion, setPublicacion] = useState(null);

    useEffect(() => {
        console.log(`Obteniendo datos para id: ${id}`);
        // Obtener los datos de la publicación del localStorage
        const publicacionGuardada = localStorage.getItem("publicacion");
        console.log(`Datos sin procesar del localStorage: ${publicacionGuardada}`);

        // Convertir la cadena JSON a objeto
        const publicacionObj = JSON.parse(publicacionGuardada);
        console.log(`Datos parseados:`, publicacionObj);

        // Convertir el id de la URL a número
        const idNumero = Number(id);

        // Verificar si la publicación existe y tiene el mismo id
        if (publicacionObj && publicacionObj.id === idNumero) {
            setPublicacion(publicacionObj);
        } else {
            // Si no se encuentra la publicación o no coincide el id, puedes manejarlo como desees
            console.log("Publicación no encontrada");
        }
    }, [id]);

    if (!publicacion) {
        return <div>Cargando...</div>;
    }

    return (
        <div className="detalle-container">
            <div className="detalle-imagen">
                <img
                    src={`data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wAARCAMGAxMDASIAAhEB`}
                    alt="imagen no encontrada"
                    className="imagen-grande"
                />
            </div>
            <div className="detalle-info">
                <h2>{publicacion.nombre}</h2>
                <p><strong>Usuario:</strong> {publicacion.user}</p>
                <p><strong>Centros:</strong> {publicacion.centros}</p>
                <p><strong>Descripción:</strong> {publicacion.descripcion}</p>
                <p><strong>Categoría:</strong> {publicacion.categoria_id}</p>
                {/* Agrega aquí cualquier otro detalle que desees mostrar */}
            </div>
        </div>
    );
};

export default PubliDetalle;

import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import "../../HarryStyles/PubliDetalle.css"; 

const PubliDetalle = () => {
    const { id } = useParams(); 
    const [publicacion, setPublicacion] = useState(null);

    useEffect(() => {
        console.log(`Obteniendo datos para id: ${id}`);
        const publicacionGuardada = localStorage.getItem("publicacion");
        console.log(`Datos sin procesar del localStorage: ${publicacionGuardada}`);

        const publicacionObj = JSON.parse(publicacionGuardada);
        console.log(`Datos parseados:`, publicacionObj);

        const idNumero = Number(id);

        if (publicacionObj && publicacionObj.id === idNumero) {
            setPublicacion(publicacionObj);
        } else {
            console.log("Publicación no encontrada");
        }
    }, [id]);

    if (!publicacion) {
        return <div>Cargando...</div>;
    }

    return (
        <div className="detalle-container">
            <div className="detalle-imagen">
                <br/><br/><br/><br/><br/><br/><br/><br/>
                 <img className="imagen-grande" src={publicacion.imagen} alt="imagen no encontrada" />
            </div>
            <div className="detalle-info">
                <br/><br/><br/><br/><br/><br/><br/><br/>
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

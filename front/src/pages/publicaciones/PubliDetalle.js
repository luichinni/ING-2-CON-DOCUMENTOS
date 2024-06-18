import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import "../../HarryStyles/PubliDetalle.css"; 
import { Link } from "react-router-dom";
import ListarComentarios from "../Comentarios/ListarComentarios";

const PubliDetalle = () => {
    const Token = localStorage.getItem('token');
    const username = localStorage.getItem('username')
    const { id } = useParams(); 
    const [publicacion, setPublicacion] = useState(null);

    useEffect(() => {
        console.log(`Obteniendo datos para id: ${id}`);
        const publicacionGuardada = localStorage.getItem("publicacion");
        console.log(`Datos sin procesar del localStorage: ${publicacionGuardada}`);

        const publicacionObj = JSON.parse(publicacionGuardada);
        console.log(`Datos parseados:`, publicacionObj);

        let nuevoArr = [];
        publicacionObj.centros.forEach((centro)=> nuevoArr.push(centro.Nombre));
        publicacionObj.centros = nuevoArr;

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

    const handleDetalleClick = () => {
        localStorage.setItem("publiOferto", publicacion.id);
        localStorage.setItem("categoriaInter", publicacion.categoria_id);
        console.log(localStorage.getItem("publiOferto"));
        console.log(localStorage.getItem("categoriaInter"));
    };

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
                <p><strong>Centros:</strong> {publicacion.centros.join(' | ')}</p>
                <p><strong>Descripción:</strong> {publicacion.descripcion}</p>
                <p><strong>Categoría:</strong> {publicacion.categoria_id}</p>
                {((Token === 'tokenUser')&&(username !== publicacion.user))?(
                <>
                    <Link to={`/InterSelePubli`} onClick={handleDetalleClick}>
                        <button className="detalle-button"> Ofrecer intercambio </button>
                    </Link>
                </>
                ):(<></>)}
                <ListarComentarios
                    publicacion={publicacion.id}
                />
            </div>
        </div>
    );
};

export default PubliDetalle;

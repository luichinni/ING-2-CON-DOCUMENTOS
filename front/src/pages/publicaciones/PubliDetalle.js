import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import "../../HarryStyles/PubliDetalle.css"; 
import { Link } from "react-router-dom";
import axios from "axios";
import ListarComentarios from "../Comentarios/ListarComentarios";

const PubliDetalle = () => {
    const Token = localStorage.getItem('token');
    const username = localStorage.getItem('username')
    const { id } = useParams(); 
    const [error, setError] = useState(false);
    const [publicacion, setPublicacion] = useState(null);
    const [valoraciones, setValoraciones] = useState('');
    const [dueño, setDueño] = useState('')

    useEffect(() => {
        console.log(`Obteniendo datos para id: ${id}`);
        const publicacionGuardada = localStorage.getItem("publicacion");
        console.log(`Datos sin procesar del localStorage: ${publicacionGuardada}`);

        const publicacionObj = JSON.parse(publicacionGuardada);
        console.log(`Datos parseados:`, publicacionObj);

        let nuevoArr = [];
        publicacionObj.centros.forEach((centro)=> nuevoArr.push(centro.nombre));
        publicacionObj.centros = nuevoArr;

        const idNumero = Number(id);

        if (publicacionObj && publicacionObj.id === idNumero) {
            setPublicacion(publicacionObj);
            setDueño(publicacionObj.user)
            console.log(`seteamos dueño: ${dueño}`)
        } else {
            console.log("Publicación no encontrada");
        }

    }, [id]);

    useEffect(() => {
        if (dueño) {
            fetchValoraciones();
        }
    }, [dueño]);

    const fetchValoraciones = async () => {
        setError('');
        try {
            const url = `http://localhost:8000/public/getValoracion?userValorado=${dueño}&token=${localStorage.getItem('token')}`;
            console.log(`llegue, url: ${url}`)
            console.log(localStorage.getItem('token'));
            const response = await axios.get(url);
            console.log(`llegue2, response:${response.data}`)

            if (!response.data || response.data.Valoracion === undefined) {
                setError('No hay valoraciones disponibles');
                setValoraciones('Sin valoraciones');
                console.log(`entre por error de undefined`)
            } else {
                setValoraciones(response.data.Valoracion);
                console.log(`entre a gurdar datos`)
            }
        } catch (error) {
            /* setError('No hay valoraciones disponibles.'); */
            setValoraciones('Sin valoraciones');
            console.error(error);
            console.log(`entre por error`)
        }
    };


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
                <p className="usuario-valoracion-container">
                    <strong>Usuario:</strong>
                        <Link className={'linkUsuario'} to={`/PubliUsuario/${publicacion.user}`}>
                            {publicacion.user}
                        </Link>
                    <div className="valoracion">
                    {(valoraciones === 'Sin valoraciones')?
                        (<>Puntuación: {valoraciones}</>):
                        (<>Puntuación: {valoraciones}/5</>)
                    }
                    </div>
                </p>
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

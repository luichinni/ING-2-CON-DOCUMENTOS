import "../HarryStyles/Intercambios.css";
import "../HarryStyles/Publicaciones.css"
import React from "react";
import Publicacion from "./Publicacion";
import { useEffect, useState } from 'react';
import axios from 'axios';
import { Link } from "react-router-dom";

const Intercambio = ({ publicacionOferta, publicacionOfertada, centro, horario, estado }) => {
  const [publi1, setPubli1] = useState([]);
  const [publi2, setPubli2] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const Token = localStorage.getItem('token');

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        console.log(publicacionOferta)
        const url1 = `http://localhost:8000/public/listarPublicaciones?id=${publicacionOferta}&token=${localStorage.getItem('token')}`;
        console.log(url1);
        const response1 = await axios.get(url1);

        if (response1.data.length === 3) {
          setError('No hay publicaciones disponibles.');
          setPubli1([]); 
        } else {
          setPubli1(procesar(response1.data));
        }
      } catch (error) {
        setError('No hay publicaciones disponibles.');
        console.error(error);
      }

      try {
        const url2 = `http://localhost:8000/public/listarPublicaciones?id=${publicacionOfertada}&token=${localStorage.getItem('token')}`;
        const response2 = await axios.get(url2);

        if (response2.data.length === 3) {
          setError('No hay publicaciones disponibles.');
          setPubli2([]); 
        } else {
          setPubli2(procesar(response2.data));
        }
      } catch (error) {
        setError('No hay publicaciones disponibles.');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [publicacionOferta, publicacionOfertada]);

  function procesar(publicaciones) {
    let publisCopy = [];
    Object.keys(publicaciones).forEach(function (clave) {
      if (!isNaN(clave)) {
        publisCopy[clave] = publicaciones[clave]
      }
    });
    return publisCopy;
  }

/*
  const handleRechazadoClick = async (e) =>{
    const formData = new FormData();
        formData.append('estado', 'rechazado');

    const respon = await axios.put(`http://localhost:8000/public/`);
    setCategorias(procesarcat(respon.data));
  }
*/
/*const handleRechazadoClick =() =>{
  //direccionar a ModificarIntercambio
}
const handleConfirmadoClick =() =>{
  //direccionar a ModificarIntercambio
}

  const handleModificarClick =() =>{
    //direccionar a ModificarIntercambio
  }*/


  return (
    <li className="intercambio-item">
      <div className="intercambio-content">
        <div className="publicaciones-container"> 
          <div className="publicacion">
            {publi1.map(publicacion => (
              <Publicacion
                key={publicacion.id} //para que no llore react
                id={publicacion.id}
                nombre={publicacion.nombre}
                descripcion={publicacion.descripcion}
                user={publicacion.user}
                categoria_id={publicacion.categoria_id}
                estado={publicacion.estado}
                imagen={publicacion.imagenes[0]?.archivo}
                centros={publicacion.centros}
              />
            ))}
          </div>
          <div className="publicacion">
            {publi2.map(publicacion => (
              <Publicacion
                key={publicacion.id}
                id={publicacion.id}
                nombre={publicacion.nombre}
                descripcion={publicacion.descripcion}
                user={publicacion.user}
                categoria_id={publicacion.categoria_id}
                estado={publicacion.estado}
                imagen={publicacion.imagenes[0]?.archivo}
		            centros={publicacion.centros}
              />
            ))}
          </div>
        </div>
        <div className="detalles">
          <p><strong>Centro:</strong> {centro}</p>
          <p><strong>Horario:</strong> {horario}</p>
          <p><strong>Estado:</strong> {estado}</p>
        
        {((Token === 'tokenAdmin'))?(
            <>
              <Link to={`/ValidarIntercambio`}>
                <button className="detalle-button"> Validar Intercambio </button>
              </Link>
            </>
          ):( 
          <>
            <Link to={`/ModificarIntercambio`} >
              <button className="detalle-button"> Modificiar </button>
            </Link>
            <Link to={`/ListarMisIntercambios`}>
              <button className="detalle-button"> Rechazar </button>
            </Link>
            { // ACA VA ALGO QUE ME DIGA SI SOY EL DUEÑO DE LA PUBLICACIÓN QUE PUEDE ACEPTAR
            }
            <Link to={`/ValidarIntercambio`}>
              <button className="detalle-button"> Confirmar </button>
            </Link>
          </>
          )
        }
        </div>
      </div>
    </li>
  );
};

export default Intercambio;
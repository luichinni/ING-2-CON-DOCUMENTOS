import "../HarryStyles/Intercambios.css";
import "../HarryStyles/Publicaciones.css"
import React, { useEffect, useState } from "react";
import axios from 'axios';
import { useNavigate } from "react-router-dom";
import Publicacion from "./Publicacion";

const Intercambio = ({ id, publicacionOferta, publicacionOfertada, centro, horario, estado, ofertaAcepta, ofertadaAcepta }) => {
  const [publi1, setPubli1] = useState([]);
  const [publi2, setPubli2] = useState([]);
  const [userPubli, setUserPubli] = useState('');
  const [userOferto, setUserOferto] = useState('');
  const [error, setError] = useState('');
  const username = localStorage.getItem('username');
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const Token = localStorage.getItem('token');

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        console.log(`oferta acepta${ofertaAcepta}`)
        console.log(`ofertada acepta${ofertadaAcepta}`)
        console.log(`username: ${username}`)
        const url1 = `http://localhost:8000/public/listarPublicaciones?id=${publicacionOferta}&token=${Token}`;
        const response1 = await axios.get(url1);
        
        if (response1.data) {
          const publicaciones = procesar(response1.data);
          setPubli1(publicaciones);
          setUserPubli(publicaciones[0]?.user || '');
          console.log(`userPubli: ${userPubli}`)
        } else {
          setError('No hay publicaciones disponibles.');
          setPubli1([]);
        }
      } catch (error) {
        setError('No hay publicaciones disponibles.');
        console.error(error);
      }

      try {
        const url2 = `http://localhost:8000/public/listarPublicaciones?id=${publicacionOfertada}&token=${Token}`;
        const response2 = await axios.get(url2);

        if (response2.data) {
          const publicaciones = procesar(response2.data);
          setPubli2(publicaciones);
          setUserOferto(publicaciones[0]?.user || '');
          console.log(`userOferto: ${userOferto}`)
        } else {
          setError('No hay publicaciones disponibles.');
          setPubli2([]);
        }
      } catch (error) {
        setError('No hay publicaciones disponibles.');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [publicacionOferta, publicacionOfertada, Token]);

  function procesar(publicaciones) {
    let publisCopy = [];
    Object.keys(publicaciones).forEach(function (clave) {
      if (!isNaN(clave)) {
        publisCopy.push(publicaciones[clave]);
      }
    });
    return publisCopy;
  }

  const handleValidarClick = () => {
    localStorage.setItem('idValidar', id);
    navigate("../ValidarIntercambio");
  };

  const handleRechazadoClick = async () => {
    try {
      const formData = new FormData();
      formData.append('id', id);
      formData.append('setestado', 'rechazado');
      const respon = await axios.put(`http://localhost:8000/public/updateIntercambio`, formData, {
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (respon.data.length === 3) {
        setError('No se realizo la modificación.');
      } else {
        window.location.reload();
      }
    } catch (error) {
      setError('No se pudo rechazar el intercambio.');
      console.error(error);
    }
  };

  const handleAceptadoClick = async () => {
    try {
      const formData = new FormData();
      formData.append('id', id);
      formData.append('setestado', 'aceptado');
      const respon = await axios.put(`http://localhost:8000/public/updateIntercambio`, formData, {
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (respon.data.length === 3) {
        setError('No se realizo la modificación.');
      } else {
        window.location.reload();
      }
    } catch (error) {
      setError('No se pudo aceptar el intercambio.');
      console.error(error);
    }
  };

  const handleModificarClick = () => {
    navigate(`../ModificarInter/${id}/${publi1[0]?.id}`);
  };

  return (
    <li className="intercambio-item">
      <br /><br /><br /><br />
      <div className="intercambio-content">
        <div className="publicaciones-container">
          <div className="publicacion">
            Publicacion
            {publi1.map(publicacion => (
              <Publicacion
                key={publicacion.id} // para que no llore react
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
            Oferta Recibida
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
          {estado === 'aceptado' || estado === 'pendiente' ? (
            Token === 'tokenAdmin' || Token === 'tokenVolunt' ? (
              <button className="detalle-button" onClick={handleValidarClick}>
                Validar Intercambio
              </button>
            ) : (
              <>
                <button className="detalle-button" onClick={handleModificarClick}>
                  Modificar
                </button>
                <button className="detalle-button" onClick={handleRechazadoClick}>
                  Rechazar
                </button>
                {((userPubli === username && ofertaAcepta === '0') || (userOferto === username && ofertadaAcepta === '0')) && (
                  <>
                  {console.log("entro")}
                  <button className="detalle-button" onClick={handleAceptadoClick}>
                    Confirmar
                  </button>
                  </>
                )}
              </>
            )
          ) : null}
        </div>
      </div>
    </li>
  );
};

export default Intercambio;

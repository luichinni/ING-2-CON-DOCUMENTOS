import "../HarryStyles/Intercambios.css";
import React from "react";
import Publicacion from "./Publicacion";
import { useEffect, useState } from 'react';
import axios from 'axios';

const Intercambio = ({ publicacion1, publicacion2, centro, horario, estado }) => {
  const [publi1, setPubli1] = useState([]);
  const [publi2, setPubli2] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const url1 = `http://localhost:8000/public/listarPublicaciones?id=${publicacion1}&token=${localStorage.getItem('token')}`;
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
        const url2 = `http://localhost:8000/public/listarPublicaciones?id=${publicacion2}&token=${localStorage.getItem('token')}`;
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
  }, [publicacion1, publicacion2]);

  function procesar(publicaciones) {
    let publisCopy = [];
    Object.keys(publicaciones).forEach(function (clave) {
      if (!isNaN(clave)) {
        publisCopy[clave] = publicaciones[clave]
      }
    });
    return publisCopy;
  }

  return (
    <li className="intercambio-item">
      <div className="intercambio-content">
        <div className="publicaciones-container">
          <div className="publicacion-1">
            {publi1.map(publicacion => (
              <Publicacion
                key={publicacion.id}
                id={publicacion.id}
                nombre={publicacion.nombre}
                descripcion={publicacion.descripcion}
                user={publicacion.user}
                categoria_id={publicacion.categoria_id}
                estado={publicacion.estado}
                imagen={publicacion.imagenes[0]?.archivo}
              />
            ))}
          </div>
          <div className="publicacion-2">
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
              />
            ))}
          </div>
        </div>
        <div className="detalles">
          <p><strong>Centro:</strong> {centro}</p>
          <p><strong>Horario:</strong> {horario}</p>
          <p><strong>Estado:</strong> {estado}</p>
        </div>
      </div>
    </li>
  );
};

export default Intercambio;

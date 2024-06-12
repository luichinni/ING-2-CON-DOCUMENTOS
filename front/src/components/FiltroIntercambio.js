import React, { useState } from 'react';
import { ButtonSubmit } from '../components/ButtonSubmit';
import "../HarryStyles/centros.css";
import "../HarryStyles/styles.css";

const FiltroIntercambio = ({ onFiltroSubmit }) => {
  const [filtro, setFiltro] = useState({
    publicacionOferta: "",
    publicacionOfertada: "",
    estado: "",
    centro: "",
    username: ""
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFiltro({
      ...filtro,
      [name]: value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onFiltroSubmit(filtro);
  };

  return (
    <>
      <br/><br/><br/>
      <form onSubmit={handleSubmit} className="filtro-form">
        <label className="filtro-label">Filtrar por:</label>
        <input 
          type="text" 
          name="publicacionOferta" 
          value={filtro.publicacionOferta} 
          onChange={handleChange} 
          placeholder="Publicación Oferta"
          className="filtro-input" 
        />
        <input 
          type="text" 
          name="publicacionOfertada" 
          value={filtro.publicacionOfertada} 
          onChange={handleChange} 
          placeholder="Publicación Ofertada"
          className="filtro-input" 
        />
        {localStorage.getItem('token') !== 'tokenUser' &&
          <input 
            type="text" 
            name="username" 
            value={filtro.username} 
            onChange={handleChange} 
            placeholder="Username"
            className="filtro-input" 
          />
        }
        <select 
          name="estado" 
          value={filtro.estado} 
          onChange={handleChange} 
          className="filtro-input"
        >
          <option value="">Seleccione un Estado</option>
          <option value="pendiente">Pendiente</option>
          <option value="cancelado">Cancelado</option>
          <option value="rechazado">Rechazado</option>
          <option value="aceptado">Aceptado</option>
          <option value="concretado">Concretado</option>
        </select>
        {localStorage.getItem('token') !== 'tokenVolunt' &&
          <input 
            type="text" 
            name="centro" 
            value={filtro.centro} 
            onChange={handleChange} 
            placeholder="Centro"
            className="filtro-input" 
          />
        }
        <button className="filtro-button" type="submit">Filtrar</button>
      </form>
    </>
  );
};

export default FiltroIntercambio;

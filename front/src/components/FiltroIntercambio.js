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
      horario:""
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
      <><br/><br/><br/>
      <form onSubmit={handleSubmit} className="filtro-form">
        <label className="filtro-label">Filtrar por:</label>
        <input type="text" name="publicacionOferta" value={filtro.publicacionOferta} onChange={handleChange} placeholder="Publicación Oferta"className="filtro-input"/>
        <input type="text" name="publicacionOfertada" value={filtro.publicacionOfertada} onChange={handleChange} placeholder="Publicación Ofertada"className="filtro-input"/>
        <input type="text" name="estado" value={filtro.estado} onChange={handleChange} placeholder="Estado"className="filtro-input"/>
        <input type="text" name="centro" value={filtro.centro} onChange={handleChange} placeholder="Centro"className="filtro-input"/>
        <input type="text" name="horario" value={filtro.horario} onChange={handleChange} placeholder="Horario"className="filtro-input"/>
        <ButtonSubmit text="Filtrar" />
      </form>
      </>
    );
  };

  export default FiltroIntercambio;

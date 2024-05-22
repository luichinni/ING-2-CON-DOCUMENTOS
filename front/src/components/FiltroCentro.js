import React, { useState } from 'react';
import { ButtonSubmit } from './ButtonSubmit';

const Filtro = ({ onFiltroSubmit }) => {
  const [filtro, setFiltro] = useState({
    key: "",
    nombre: "",
    direccion: "",
    hora_abre: "",
    hora_cierra: ""
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
    <form onSubmit={handleSubmit}>
      <input type="text" name="key" value={filtro.nombre} onChange={handleChange} placeholder="Numero de centro" />
      <input type="text" name="nombre" value={filtro.user} onChange={handleChange} placeholder="Nombre" />
      <input type="text" name="direccion" value={filtro.categoria} onChange={handleChange} placeholder="Direccion" />
      <input type="text" name="hora_abre" value={filtro.estado} onChange={handleChange} placeholder="Hora_abre" />
      <input type="text" name="hora_cierra" value={filtro.estado} onChange={handleChange} placeholder="Hora_cierra" />
      <ButtonSubmit text="Filtrar" />
    </form>
  );
}

export default Filtro;
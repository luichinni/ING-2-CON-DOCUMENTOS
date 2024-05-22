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
      <input type="text" name="key" value={filtro.key} onChange={handleChange} placeholder="Numero de centro" />
      <input type="text" name="nombre" value={filtro.nombre} onChange={handleChange} placeholder="Nombre" />
      <input type="text" name="direccion" value={filtro.categoria} onChange={handleChange} placeholder="Direccion" />
      <ButtonSubmit text="Filtrar" />
    </form>
  );
}

export default Filtro;
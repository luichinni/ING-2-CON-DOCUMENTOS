import React, { useState } from 'react';
import { ButtonSubmit } from '../components/ButtonSubmit'

const FiltroUsuario = ({ onFiltroSubmit }) => {
  const [filtro, setFiltro] = useState({
    userName: "",
    nombre: "",
    apellido: "",
    dni:""
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
        <label> Filtrar por: </label>
        <input type="text" name="userName" value={filtro.userName} onChange={handleChange} placeholder="Nombre de usuario" />
        <input type="text" name="nombre" value={filtro.nombre} onChange={handleChange} placeholder="Nombre" />
        <input type="text" name="apellido" value={filtro.apellido} onChange={handleChange} placeholder="Apellido" />
        <input type="text" name="dni" value={filtro.dni} onChange={handleChange} placeholder="DNI" />
        <select onChange={handleCentrosChange}>
            <option value="user">Usuario</option>
            <option value="volunt">Voluntario</option>
            <option value="admin">Administrador</option>
        </select>
      <ButtonSubmit text="Filtrar" />
    </form>
  );
}

export default FiltroUsuario;
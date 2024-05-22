import React from 'react';
import { useNavigate } from 'react-router-dom';

export function ButtonCerrarSesion() {
  const navigate = useNavigate();

  const handleClick = () => {
    localStorage.clear();
    navigate('../');
    window.location.reload();
  };

  return (
    <button onClick={handleClick}>
      Cerrar Sesion
    </button>
  );
}

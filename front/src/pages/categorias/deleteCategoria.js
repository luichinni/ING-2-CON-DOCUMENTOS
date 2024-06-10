import React, { useEffect, useRef } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";

const DeleteCategoria = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const hasMounted = useRef(false);

  useEffect(() => {
    const deleteCategoria = async () => {
      try {
        if (window.confirm('¿Seguro que querés eliminar la categoría?')) {
          await axios.delete(`http://localhost:8000/public/deleteCategoria?id=${id}`);
          alert(`Categoría eliminada`);
          navigate(`../Categorias`);
        } else {
          navigate(`../Categorias`);
        }
      } catch (error) {
        alert(error);
      }
    };
    if(!hasMounted.current){
      deleteCategoria();
      hasMounted.current = true;
    }
  }, []); // Pasamos un array vacío como segundo argumento

  return null; // No renderizamos nada en esta página
};

export default DeleteCategoria;



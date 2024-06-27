import React, { useEffect, useRef } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";

const DeleteComentario = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const hasMounted = useRef(false);

  useEffect(() => {
    const deleteCategoria = async () => {
      try {
        if (window.confirm('¿Seguro que querés eliminar el comentario?')) {
          await axios.delete(`http://localhost:8000/public/deleteComentario?id=${id}`);
          alert(`Categoría eliminada`);
          window.location.reload;
        } else {
            window.location.reload;
        }
      } catch (error) {
        alert('No se pudo eliminar la comentario');
        window.location.reload;
      }
    };
    if(!hasMounted.current){
      deleteComentario();
      hasMounted.current = true;
    }
  }, []); // Pasamos un array vacío como segundo argumento

  return null; // No renderizamos nada en esta página
};

export default DeleteComentario;
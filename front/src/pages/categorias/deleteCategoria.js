import React, { useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";

const DeleteCategoria = () => {
  const navigate = useNavigate();
  const { id } = useParams();

  useEffect(() => {
    const deleteCategoria = async () => {
      try {
        if (window.confirm('¿Seguro que querés eliminar la categoría?')) {
          await axios.delete(`http://localhost:8000/public/deleteCategoria/${id}`);
          alert(`Categoría eliminada`);
          navigate(`../Categorias`);
        } else {
          navigate(`../Categorias`);
        }
      } catch (error) {
        alert(error);
      }
    };
    deleteCategoria();
  }, [id, navigate]);

  return null; // No renderizamos nada en esta página
};

export default DeleteCategoria;

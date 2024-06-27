import { useEffect, useRef } from "react";
import { useParams, navigate } from "react-router-dom";
import axios from "axios";

const DeleteComentario = ({id, userMod}) => {
  //const { id } = useParams();
  const hasMounted = useRef(false);

  useEffect(() => {
    const deleteComentario = async () => {
      console.log(id + ' ' + userMod)
      try {
        if (window.confirm('¿Seguro que querés eliminar este comentario?')) {
          await axios.delete(`http://localhost:8000/public/deleteComentario?id=${id}&&userMod=${userMod}`);
          alert(`Comentario eliminado`);
          window.location.reload();
        }
      } catch (error) {
        alert('No se pudo eliminar el comentario');
        
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
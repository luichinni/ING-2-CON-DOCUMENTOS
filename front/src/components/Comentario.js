import { Link } from 'react-router-dom';
import '../HarryStyles/Comentario.css';
import DeleteComentario from '../pages/Comentarios/DeleteComentario';
import { useEffect, useState } from 'react';

const Comentario = ({ id, user, texto, respondeA, fecha_publicacion }) => {

  const [ELIMINAR,setEliminar] = useState(false);

  function handleBorrar(){
    console.log(id + ' ' + localStorage.getItem('username'))
    setEliminar(true);
  }

  useEffect(()=>{
    if (ELIMINAR == true) setEliminar(false);
  },[ELIMINAR])

  return (
    <fieldset className="comentario">
      <div className="comentario-info">
        <p className="user">{user}</p>
        <p className="texto">{texto}</p>
        {respondeA && (
          <p className="respondeA">Responde a: {respondeA}</p>
        )}
        <p className="fecha">Publicado el: {new Date(fecha_publicacion).toLocaleDateString()}</p>

        {// si soy un admin, el dueño de la publicación o la persona que comento
          <button onClick={handleBorrar}> Eliminar </button>
        }
        {ELIMINAR && (
          <DeleteComentario 
            id={id} 
            userMod={localStorage.getItem('username')}/>
        )

        }
      </div>
    </fieldset>
  );
}

export default Comentario;

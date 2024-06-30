import '../HarryStyles/Comentario.css';
import '../HarryStyles/Notificaciones.css';
import DeleteComentario from '../pages/Comentarios/DeleteComentario';
import { useEffect, useState } from 'react';
//<<<<<<< Updated upstream
import ModificarComentario from '../pages/Comentarios/ModificarComentario';
//=======
import { CiTrash } from 'react-icons/ci'; 
import { MdEdit } from "react-icons/md";
//>>>>>>> Stashed changes

const Comentario = ({ id, user, texto, respondeA, fecha_publicacion }) => {

  const [ELIMINAR,setEliminar] = useState(false);
  const username = localStorage.getItem('username');

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
          <button onClick={handleBorrar} className='botonCampanita'> <CiTrash size={26} className='botonCampanita' /> </button>
        }
        {ELIMINAR && (
          <DeleteComentario 
            id={id} 
            userMod={localStorage.getItem('username')} 
            />
        )
        }
        { /*(user === username)&&(
          <ModificarComentario
            id={id}
            userMod={localStorage.getItem('username')}
          />
        )
         */ }
      </div>
    </fieldset>
  ); 
}

export default Comentario;

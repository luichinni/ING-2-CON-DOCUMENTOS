import { Link } from 'react-router-dom';
import '../HarryStyles/Comentario.css';

const Comentario = ({ id, user, texto, respondeA, fecha_publicacion }) => {
  return (
    <fieldset className="comentario">
      <div className="comentario-info">
        <p className="user">{user}</p>
        <p className="texto">{texto}</p>
        {respondeA && (
          <p className="respondeA">Responde a: {respondeA}</p>
        )}
        <p className="fecha">Publicado el: {new Date(fecha_publicacion).toLocaleDateString()}</p>
      </div>
    </fieldset>
  );
}

export default Comentario;

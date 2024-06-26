import axios from 'axios';
import '../../HarryStyles/Notificaciones.css';
import { useEffect, useState } from 'react';
import Notificacion from './Notificacion';

let llamada = 0;
const ListarNotis = () => {
  const [notificaciones, setNotificaciones] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');
      try {
        const url = `http://localhost:8000/public/listarNotificaciones?user=${localStorage.getItem('username')}&token=${localStorage.getItem('token')}`;
        const response = await axios.get(url);
        if (response.data.length === 0) {
          setError('No hay notificaciones disponibles.');
        } else {
          console.log(llamada);
          if (llamada%2 === 0){
            localStorage.setItem('notis',JSON.stringify(response.data));
            console.log('entra');
          }
          llamada++;
          setNotificaciones(procesar(JSON.parse(localStorage.getItem('notis')))); // Mostrar solo las primeras 5 notificaciones
        }
      } catch (error) {
        setError('No hay notificaciones disponibles.');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  function procesar(notifica) {
    let notisCopy = [];
    Object.keys(notifica).forEach(function (clave) {
      if (!isNaN(clave)) {
        notisCopy[clave] = notifica[clave]
      }
    });
    return notisCopy;
  }

  return (
    <div className='notiContent'>
      {loading ? (
        <h1 className='cargando'>Cargando...</h1>
      ) : error ? (
        <h1 className='sin-noti'>{error}</h1>
      ) : (
        notificaciones.map(notificacion => (
          <Notificacion
            key={notificacion.id} //para que no llore react
            user={notificacion.user}
            texto={notificacion.texto}
            fecha={notificacion.created_at}
            url={notificacion.url}
            visto={notificacion.visto}
          />
        ))
      )}
      {notificaciones.length === 5 && (
        <p className='ver-mas'>Ver más...</p> // O un enlace a la página de notificaciones completas
      )}
    </div>
  );
}

export default ListarNotis;
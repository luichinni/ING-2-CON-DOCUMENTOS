import axios from 'axios';
import '../../HarryStyles/Notificaciones.css';
import { useEffect, useState } from 'react';
import Notificacion from './Notificacion';

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
          setNotificaciones(response.data.slice(0, 5)); // Mostrar solo las primeras 5 notificaciones
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
            fecha={notificacion.fecha}
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

export const formatDate = (dateString, options = {}) => {
    return new Date(dateString).toLocaleDateString(undefined, options);
  };
  
  export const handleApiError = (error, defaultMessage = 'An error occurred') => {
    console.error(error);
    return error.response?.data?.message || defaultMessage;
  };
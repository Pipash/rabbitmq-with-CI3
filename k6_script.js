import http from 'k6/http';
import { sleep } from 'k6';

export const options = {
  //vus: 1000,
  //duration: '30s',
  scenarios: {
    constant_request_rate: {
      executor: 'constant-arrival-rate',
      rate: 100,
      timeUnit: '1s',
      duration: '3m',
      preAllocatedVUs: 20,
      maxVUs: 1500,
    },
  },
};

export default function () {
  /*for (var i = 1; i <= 30000; i++) {
    //http.get('http://localhost/codeIgniter-3/index.php/welcome/publisher/'+i);
    http.get("http://localhost/codeIgniter-3/index.php/welcome/call_publisher");
  }*/
  //http.get("http://localhost/codeIgniter-3/index.php/welcome/call_publisher");
  http.get('http://localhost/codeIgniter-3/index.php/welcome/publisher');
  sleep(1); 
}